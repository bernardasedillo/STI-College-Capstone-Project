<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

$isAjaxRequest = (
    (isset($_GET['archive']) && $_GET['archive'] === 'true') || 
    (isset($_GET['data']) && $_GET['data'] === 'true') || 
    $_SERVER['REQUEST_METHOD'] === 'POST' || 
    $_SERVER['REQUEST_METHOD'] === 'PUT'
);

if ($isAjaxRequest) {
    header('Content-Type: application/json; charset=utf-8');
    header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
    header("Cache-Control: post-check=0, pre-check=0", false);
    header("Pragma: no-cache");
} else {
    header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
    header("Cache-Control: post-check=0, pre-check=0", false);
    header("Pragma: no-cache");
    header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");
}

require_once '../includes/validate.php';
require '../includes/name.php';
require_once 'log_activity.php';

$jsonFile = '../json files/questions-answers.json';
$archiveFile = '../json files/archive-questions-answers.json';

function readJsonFile($filePath) {
    if (!file_exists($filePath)) {
        return [];
    }
    
    $content = file_get_contents($filePath);
    if ($content === false) {
        return [];
    }
    
    $data = json_decode($content, true);
    if (json_last_error() !== JSON_ERROR_NONE) {
        error_log("JSON decode error in $filePath: " . json_last_error_msg());
        return [];
    }
    
    return $data ?: [];
}

function writeJsonFile($filePath, $data) {
    $dir = dirname($filePath);
    if (!is_dir($dir)) {
        if (!mkdir($dir, 0755, true)) {
            throw new Exception("Failed to create directory: $dir");
        }
    }
    
    $jsonData = json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
    if ($jsonData === false) {
        throw new Exception("Failed to encode JSON data: " . json_last_error_msg());
    }
    
    if (file_put_contents($filePath, $jsonData, LOCK_EX) === false) {
        throw new Exception("Failed to write file: $filePath");
    }
}

function sendJsonResponse($data, $statusCode = 200) {
    http_response_code($statusCode);
    echo json_encode($data, JSON_UNESCAPED_UNICODE);
    exit;
}

function sendErrorResponse($message, $statusCode = 400) {
    sendJsonResponse(['error' => $message], $statusCode);
}

function validateInput($data, $required = []) {
    foreach ($required as $field) {
        if (!isset($data[$field]) || (is_string($data[$field]) && trim($data[$field]) === '')) {
            throw new Exception("Required field '$field' is missing or empty");
        }
    }
    return true;
}

// Initialize message variable
$message = '';
if (isset($_SESSION['success_message'])) {
    $message = '<div class="warning alert-success" 
        style="font-size:13px; padding:18px; margin:20px 0; text-align:left; font-weight:bold; border-radius:6px;">'
        . htmlspecialchars($_SESSION['success_message']) . 
    '</div>';
    unset($_SESSION['success_message']);
}

try {
    if ($_SERVER['REQUEST_METHOD'] === 'GET') {
        if (isset($_GET['archive']) && $_GET['archive'] === 'true') {
            $data = readJsonFile($archiveFile);
            sendJsonResponse($data);
        } elseif (isset($_GET['data']) && $_GET['data'] === 'true') {
            $data = readJsonFile($jsonFile);
            sendJsonResponse($data);
        }
    }

    elseif ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $input = file_get_contents('php://input');
        if ($input === false) {
            sendErrorResponse('Failed to read request body', 500);
        }

        $requestData = json_decode($input, true);
        if (json_last_error() !== JSON_ERROR_NONE) {
            sendErrorResponse('Invalid JSON in request: ' . json_last_error_msg());
        }

        if (isset($requestData['question'])) {
            $userQuestion = trim($requestData['question']);
            if (empty($userQuestion)) {
                sendErrorResponse('Question cannot be empty');
            }

            $userQuestion = strtolower($userQuestion);
            $data = readJsonFile($jsonFile);
            $response = ['answer' => "Sorry, I don't have an answer for that."];

            foreach ($data as $category => $qa) {
                if (isset($qa['questions']) && is_array($qa['questions'])) {
                    foreach ($qa['questions'] as $dbQuestion) {
                        $dbQuestion = strtolower(trim($dbQuestion));
                        $cleanDbQuestion = html_entity_decode(strip_tags($dbQuestion));
                        $cleanUserQuestion = html_entity_decode(strip_tags($userQuestion));

                        if (strpos($cleanDbQuestion, $cleanUserQuestion) !== false || 
                            strpos($cleanUserQuestion, $cleanDbQuestion) !== false) {
                            $response['answer'] = $qa['answer'];
                            break 2;
                        }
                    }
                }
            }

            sendJsonResponse($response);
        }

        elseif (isset($requestData['action']) && isset($requestData['categories'])) {
            $action = $requestData['action'];
            $selectedCategories = $requestData['categories'];

            if (!in_array($action, ['archive'])) {
                sendErrorResponse('Invalid action. Must be "archive"');
            }

            if (!is_array($selectedCategories) || empty($selectedCategories)) {
                sendErrorResponse('Categories must be a non-empty array');
            }

            $data = readJsonFile($jsonFile);
            $archiveData = readJsonFile($archiveFile);

            if ($action === 'archive') {
                foreach ($selectedCategories as $category) {
                    if (isset($data[$category])) {
                        $archiveData[$category] = $data[$category];
                        unset($data[$category]);
                    }
                }
            }

            writeJsonFile($jsonFile, $data);
            writeJsonFile($archiveFile, $archiveData);
            log_activity($_SESSION['admin_id'], 'Chatbot Management', 'Archived chatbot categories: ' . implode(', ', $selectedCategories));

            $_SESSION['success_message'] = 'Chatbot Contents updated successfully!';
            http_response_code(200);
            echo json_encode(['redirect' => true]);
            exit;
        } else {
            sendErrorResponse('Invalid request data');
        }
    }

    elseif ($_SERVER['REQUEST_METHOD'] === 'PUT') {
        $input = file_get_contents('php://input');
        if ($input === false) {
            sendErrorResponse('Failed to read request body', 500);
        }

        $newCategoryData = json_decode($input, true);
        if (json_last_error() !== JSON_ERROR_NONE) {
            sendErrorResponse('Invalid JSON in request: ' . json_last_error_msg());
        }

        try {
            validateInput($newCategoryData, ['category', 'questions', 'answer']);
        } catch (Exception $e) {
            sendErrorResponse($e->getMessage());
        }

        $newCategoryName = trim($newCategoryData['category']);
        $originalCategoryName = isset($newCategoryData['originalCategory']) ? trim($newCategoryData['originalCategory']) : $newCategoryName;
        $questions = $newCategoryData['questions'];
        $answer = trim($newCategoryData['answer']);

        if (!is_array($questions) || empty($questions)) {
            sendErrorResponse('Questions must be a non-empty array');
        }

        $questions = array_filter(array_map('trim', $questions));
        if (empty($questions)) {
            sendErrorResponse('At least one valid question is required');
        }

        $data = readJsonFile($jsonFile);

        if (
            strtolower($originalCategoryName) !== strtolower($newCategoryName) &&
            isset($data[$originalCategoryName])
        ) {
            unset($data[$originalCategoryName]);
        }

        $data[$newCategoryName] = [
            'questions' => $questions,
            'answer' => $answer
        ];

        $isUpdate = (strtolower($originalCategoryName) === strtolower($newCategoryName));
        $successType = $isUpdate ? 'saved' : 'added';
        $message = $isUpdate ? 'Chatbot Contents updated successfully' : 'Chatbot Contents updated successfully';

        writeJsonFile($jsonFile, $data);
        log_activity($_SESSION['admin_id'], 'Chatbot Management', $message . ': ' . $newCategoryName);
        
        $_SESSION['success_message'] = $message . '!';
        sendJsonResponse(['redirect' => true, 'success' => $successType]);
    }

} catch (Exception $e) {
    error_log("Chatbot Admin Error: " . $e->getMessage());
    if ($isAjaxRequest) {
        sendErrorResponse('An error occurred: ' . $e->getMessage(), 500);
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../assets/css/admin/modern-chatbot.css">
    <link rel="icon" href="../assets/favicon.ico">
    <meta http-equiv="Cache-Control" content="no-cache, no-store, must-revalidate">
    <meta http-equiv="Pragma" content="no-cache">
    <meta http-equiv="Expires" content="0">
    <title>Chatbot Admin</title>
</head>
<body>
<div class="container-fluid">
    <h3>Chatbot Contents</h3>
    
    <!-- Success Message Display Area -->
    <?php echo $message; ?>

    <div class="card">
        <div class="card-header">
            <h2>Manage Categories</h2>
        </div>
        <div class="card-body">
            <button class="btn btn-primary" onclick="openAddCategoryModal()">Add Category</button>
            <button class="btn btn-warning" onclick="archiveCategory()">Archive Category</button>
        </div>
    </div>

    <div class="card">
        <div class="card-header">
            <h2>Edit Content</h2>
        </div>
        <div class="card-body">
            <div class="form-group">
                <label for="category">Select Category:</label>
                <select id="category" class="form-control" onchange="loadCategoryData()">
                    <option value="" disabled selected>Loading categories...</option>
                </select>
            </div>
            <div class="form-group">
                <label for="jsonDataCategory">Category</label>
                <input type="text" id="jsonDataCategory" class="form-control" placeholder="Category will display here...." disabled>
            </div>
            <div class="form-group">
                <label for="jsonDataQuestions">Questions</label>
                <textarea id="jsonDataQuestions" class="form-control" rows="8" placeholder="Questions will display here...." disabled></textarea>
            </div>
            <div class="form-group">
                <label for="jsonDataAnswers">Answer</label>
                <textarea id="jsonDataAnswers" class="form-control" rows="8" placeholder="Answer will display here...." disabled></textarea>
            </div>
            <button class="btn btn-success" id="saveChangesButton" disabled>Save Changes</button>
        </div>
    </div>
</div>

<div class="modal" id="addCategoryModal">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" onclick="closeAddCategoryModal()">&times;</button>
                <h4 class="modal-title">Add New Category</h4>
            </div>
            <div class="modal-body">
                <div class="form-group">
                    <label for="newcategory">Category:</label>
                    <input type="text" class="form-control" id="newcategory" placeholder="Enter category...">
                    <div id="categoryErrorMessage" class="error-message" style="display: none;"></div>
                    <div id="categoryErrorMessage1" class="error-message" style="display: none;"></div>
                </div>
                <div class="form-group">
                    <label for="newquestions">Questions: <span>(one per line)</span></label>
                    <textarea class="form-control" id="newquestions" rows="8" placeholder="Enter questions..."></textarea>
                    <div id="questionsErrorMessage" class="error-message" style="display: none;"></div>
                </div>
                <div class="form-group">
                    <label for="newanswer">Answer:</label>
                    <textarea class="form-control" id="newanswer" rows="8" placeholder="Enter answer..."></textarea>
                    <div id="answerErrorMessage" class="error-message" style="display: none;"></div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-primary" onclick="submitCategory()">Submit</button>
                <button type="button" class="btn btn-secondary" onclick="closeAddCategoryModal()">Close</button>
            </div>
        </div>
    </div>
</div>

<div class="modal" id="archiveCategoryModal">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" onclick="closeArchiveModal()">&times;</button>
                <h4 class="modal-title">Archive Categories</h4>
            </div>
            <div class="modal-body">
                <div id="categoryList"></div>
                <div id="archiveErrorMessage" class="error-message" style="display: none;"></div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-danger" onclick="submitArchive()">Archive Selected</button>
                <button type="button" class="btn btn-secondary" onclick="closeArchiveModal()">Close</button>
            </div>
        </div>
    </div>
</div>

<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/js/bootstrap.min.js"></script>
<script>
    let initialCategory = '';
    let initialQuestions = '';
    let initialAnswers = '';
    let currentSelectedCategory = '';
    let retryTimeout = null;
    const MAX_LOADING_ATTEMPTS = 5;

    function formatCategoryForDisplay(categoryName) { 
        return categoryName.replace(/_/g, ' '); 
    }
    
    function formatCategoryForStorage(categoryDisplayName) { 
        return categoryDisplayName.replace(/ /g, '_'); 
    }

    async function fetchCategories(selectCategoryAfterLoad = null, retryCount = 0) {

        const categorySelect = document.getElementById('category');
        if (!categorySelect) {
            console.error('[FETCH] Category select not found');
            return false;
        }
        
        try {
            const timestamp = Date.now() + '-' + Math.random().toString(36).substr(2, 9);
            const url = `AdminChatbot.php?data=true&_=${timestamp}`;
            
            const controller = new AbortController();
            const timeoutId = setTimeout(() => controller.abort(), 10000);
            
            const response = await fetch(url, {
                method: 'GET',
                headers: {
                    'Cache-Control': 'no-cache, no-store, must-revalidate',
                    'Pragma': 'no-cache',
                    'Expires': '0'
                },
                signal: controller.signal
            });
            
            clearTimeout(timeoutId);
            
            if (!response.ok) {
                throw new Error(`HTTP ${response.status}`);
            }
            
            const contentType = response.headers.get('content-type');
            if (!contentType || !contentType.includes('application/json')) {
                const text = await response.text();
                console.error('[FETCH] Not JSON:', text.substring(0, 200));
                throw new Error('Server did not return JSON');
            }
            
            const data = await response.json();
            const categories = Object.keys(data).sort((a, b) => a.localeCompare(b));
            
            const fragment = document.createDocumentFragment();
            const defaultOption = document.createElement('option');
            defaultOption.value = '';
            defaultOption.disabled = true;
            defaultOption.selected = true;
            defaultOption.textContent = categories.length === 0 ? 'No categories available' : '-- Select a Category --';
            fragment.appendChild(defaultOption);
            
            categories.forEach(categoryKey => {
                const option = document.createElement('option');
                option.value = categoryKey;
                option.textContent = formatCategoryForDisplay(categoryKey);
                fragment.appendChild(option);
            });
            
            categorySelect.innerHTML = '';
            categorySelect.appendChild(fragment);

            let categoryToSelect = null;
            if (selectCategoryAfterLoad && categories.includes(selectCategoryAfterLoad)) {
                categoryToSelect = selectCategoryAfterLoad;
            } else if (currentSelectedCategory && categories.includes(currentSelectedCategory)) {
                categoryToSelect = currentSelectedCategory;
            }

            if (categoryToSelect) {
                categorySelect.value = categoryToSelect;
                await loadCategoryData();
            } else {
                clearContentDisplay();
            }
            
            return true;

        } catch (error) {
            console.error(`[FETCH] Error: ${error.message}`);
            
            if (retryCount < MAX_LOADING_ATTEMPTS - 1) {
                const retryDelay = Math.min(1000 * Math.pow(2, retryCount), 5000);
                categorySelect.innerHTML = `<option value="" disabled selected>Loading (${retryCount + 2}/${MAX_LOADING_ATTEMPTS})...</option>`;
                
                return new Promise(resolve => {
                    retryTimeout = setTimeout(async () => {
                        const result = await fetchCategories(selectCategoryAfterLoad, retryCount + 1);
                        resolve(result);
                    }, retryDelay);
                });
            } else {
                categorySelect.innerHTML = '<option value="" disabled selected>Error - Please refresh page</option>';
                return false;
            }
        }
    }

    function clearContentDisplay() {
        document.getElementById('jsonDataCategory').value = '';
        document.getElementById('jsonDataQuestions').value = '';
        document.getElementById('jsonDataAnswers').value = '';
        document.getElementById('jsonDataCategory').disabled = true;
        document.getElementById('jsonDataQuestions').disabled = true;
        document.getElementById('jsonDataAnswers').disabled = true;
        document.getElementById('saveChangesButton').disabled = true;
        initialCategory = '';
        initialQuestions = '';
        initialAnswers = '';
        currentSelectedCategory = '';
    }

    async function loadCategoryData() {
        const selectedCategoryKey = document.getElementById('category').value;
        if (!selectedCategoryKey) {
            clearContentDisplay();
            return;
        }
        currentSelectedCategory = selectedCategoryKey;
        
        try {
            const timestamp = Date.now() + '-' + Math.random().toString(36).substr(2, 9);
            const response = await fetch(`AdminChatbot.php?data=true&_=${timestamp}`, {
                headers: {
                    'Cache-Control': 'no-cache, no-store, must-revalidate',
                    'Pragma': 'no-cache',
                    'Expires': '0'
                }
            });
            
            if (!response.ok) throw new Error('Network response was not ok');
            
            const data = await response.json();
            const selectedCategoryData = data[selectedCategoryKey];
            
            if (selectedCategoryData) {
                const formattedAnswer = selectedCategoryData.answer.replace(/<br\s*\/?>/gi, '\n').replace(/&nbsp;/g, ' ');
                const formattedQuestions = selectedCategoryData.questions.map(q => 
                    q.replace(/<br\s*\/?>/gi, '\n').replace(/&nbsp;/g, ' ')
                ).join('\n');
                
                document.getElementById('jsonDataCategory').value = formatCategoryForDisplay(selectedCategoryKey);
                document.getElementById('jsonDataQuestions').value = formattedQuestions;
                document.getElementById('jsonDataAnswers').value = formattedAnswer;
                
                initialCategory = formatCategoryForDisplay(selectedCategoryKey);
                initialQuestions = formattedQuestions;
                initialAnswers = formattedAnswer;
                
                document.getElementById('jsonDataCategory').disabled = false;
                document.getElementById('jsonDataQuestions').disabled = false;
                document.getElementById('jsonDataAnswers').disabled = false;
            } else {
                clearContentDisplay();
            }
        } catch (error) {
            console.error('[LOAD] Error:', error);
        }
        
        toggleSaveButton();
    }

    async function saveChanges() {
        const originalCategoryKey = currentSelectedCategory;
        const updatedCategoryDisplayName = document.getElementById('jsonDataCategory').value.trim();
        const newCategoryStorageKey = formatCategoryForStorage(updatedCategoryDisplayName);
        const updatedQuestions = document.getElementById('jsonDataQuestions').value
            .split('\n')
            .filter(q => q.trim())
            .map(q => q.trim().replace(/ /g, '&nbsp;').replace(/\n/g, '<br>'));
        const updatedAnswer = document.getElementById('jsonDataAnswers').value
            .trim()
            .replace(/ /g, '&nbsp;')
            .replace(/\n/g, '<br>');
        
        if (!updatedCategoryDisplayName || updatedQuestions.length === 0 || !updatedAnswer) {
            alert('All fields are required.');
            return;
        }
        
        try {
            const response = await fetch('AdminChatbot.php', {
                method: 'PUT',
                headers: { 
                    'Content-Type': 'application/json',
                    'Cache-Control': 'no-cache, no-store, must-revalidate'
                },
                body: JSON.stringify({ 
                    category: newCategoryStorageKey, 
                    originalCategory: originalCategoryKey, 
                    questions: updatedQuestions, 
                    answer: updatedAnswer 
                })
            });
            
            if (!response.ok) {
                const errorData = await response.json();
                throw new Error(errorData.error || 'Failed to save changes.');
            }
            
            const result = await response.json();
            if (result.redirect) {
                const timestamp = Date.now() + '-' + Math.random().toString(36).substr(2, 9);
                window.location.replace(`${window.location.pathname}?_=${timestamp}`);
            }
        } catch (error) {
            console.error('[SAVE] Error:', error);
            alert('Failed to save changes: ' + error.message);
        }
    }

    function toggleSaveButton() {
        const isCategorySelected = document.getElementById('category').value !== '';
        if (!isCategorySelected) {
            document.getElementById('saveChangesButton').disabled = true;
            return;
        }
        
        const categoryInput = document.getElementById('jsonDataCategory').value.trim();
        const questionsInput = document.getElementById('jsonDataQuestions').value.trim();
        const answersInput = document.getElementById('jsonDataAnswers').value.trim();
        const isEdited = categoryInput !== initialCategory || 
                        questionsInput !== initialQuestions || 
                        answersInput !== initialAnswers;
        
        document.getElementById('saveChangesButton').disabled = !isEdited;
    }

    function openAddCategoryModal() {
        document.getElementById('addCategoryModal').classList.add('modal-active');
    }

    function closeAddCategoryModal() {
        document.getElementById('addCategoryModal').classList.remove('modal-active');
        document.getElementById('newcategory').value = '';
        document.getElementById('newquestions').value = '';
        document.getElementById('newanswer').value = '';
        document.getElementById("categoryErrorMessage").style.display = 'none';
        document.getElementById("categoryErrorMessage1").style.display = 'none';
        document.getElementById("questionsErrorMessage").style.display = 'none';
        document.getElementById("answerErrorMessage").style.display = 'none';
    }

    async function submitCategory() {
        const category = document.getElementById('newcategory').value.trim();
        const questions = document.getElementById('newquestions').value
            .split('\n')
            .filter(q => q.trim())
            .map(q => q.trim());
        const answer = document.getElementById('newanswer').value.trim();

        document.getElementById("categoryErrorMessage").style.display = 'none';
        document.getElementById("categoryErrorMessage1").style.display = 'none';
        document.getElementById("questionsErrorMessage").style.display = 'none';
        document.getElementById("answerErrorMessage").style.display = 'none';

        let hasError = false;

        if (!category) {
            document.getElementById("categoryErrorMessage").textContent = 'Category is required.';
            document.getElementById("categoryErrorMessage").style.display = 'block';
            hasError = true;
        }

        if (questions.length === 0) {
            document.getElementById("questionsErrorMessage").textContent = 'At least one question is required.';
            document.getElementById("questionsErrorMessage").style.display = 'block';
            hasError = true;
        }

        if (!answer) {
            document.getElementById("answerErrorMessage").textContent = 'Answer is required.';
            document.getElementById("answerErrorMessage").style.display = 'block';
            hasError = true;
        }

        if (hasError) return;

        try {
            const formattedQuestions = questions.map(q => 
                q.replace(/ /g, '&nbsp;').replace(/\n/g, '<br>')
            );
            const formattedAnswer = answer.replace(/ /g, '&nbsp;').replace(/\n/g, '<br>');
            const categoryKey = formatCategoryForStorage(category);

            const response = await fetch('AdminChatbot.php', {
                method: 'PUT',
                headers: { 
                    'Content-Type': 'application/json',
                    'Cache-Control': 'no-cache, no-store, must-revalidate'
                },
                body: JSON.stringify({ 
                    category: categoryKey, 
                    questions: formattedQuestions, 
                    answer: formattedAnswer 
                })
            });

            if (!response.ok) {
                const errorData = await response.json();
                throw new Error(errorData.error || 'Failed to add category.');
            }

            const result = await response.json();
            if (result.redirect) {
                const timestamp = Date.now() + '-' + Math.random().toString(36).substr(2, 9);
                window.location.replace(`${window.location.pathname}?_=${timestamp}`);
            }
        } catch (error) {
            console.error('[ADD] Error:', error);
            document.getElementById("categoryErrorMessage1").textContent = 'Error: ' + error.message;
            document.getElementById("categoryErrorMessage1").style.display = 'block';
        }
    }
    
    function archiveCategory() {
        const modal = document.getElementById('archiveCategoryModal');
        modal.classList.add('modal-active');
        document.getElementById('categoryList').innerHTML = '<p>Loading categories...</p>';
        
        const timestamp = Date.now() + '-' + Math.random().toString(36).substr(2, 9);
        fetch(`AdminChatbot.php?data=true&_=${timestamp}`, {
            headers: {
                'Cache-Control': 'no-cache, no-store, must-revalidate',
                'Pragma': 'no-cache',
                'Expires': '0'
            }
        })
        .then(response => {
            if (!response.ok) {
                return response.json().then(data => { 
                    throw new Error(data.error || 'Network error'); 
                });
            }
            return response.json();
        })
        .then(data => {
            const categoryListDiv = document.getElementById('categoryList');
            categoryListDiv.innerHTML = '';
            const categories = Object.keys(data);
            
            if (categories.length > 0) {
                categories.forEach(categoryKey => {
                    const wrapperDiv = document.createElement('div');
                    wrapperDiv.className = 'category-item';
                    
                    const checkbox = document.createElement('input');
                    checkbox.type = 'checkbox';
                    checkbox.id = `archive-category-${categoryKey}`;
                    checkbox.name = 'archive_category';
                    checkbox.value = categoryKey;
                    checkbox.className = 'category-checkbox';
                    
                    const label = document.createElement('label');
                    label.htmlFor = `archive-category-${categoryKey}`;
                    label.textContent = formatCategoryForDisplay(categoryKey);
                    label.className = 'category-label';
                    
                    wrapperDiv.append(checkbox, label);
                    wrapperDiv.addEventListener('click', (event) => {
                        if (event.target !== checkbox) {
                            checkbox.checked = !checkbox.checked;
                        }
                        clearArchiveErrorMessage();
                    });
                    
                    categoryListDiv.appendChild(wrapperDiv);
                });
            } else {
                categoryListDiv.innerHTML = '<p>No categories available to archive.</p>';
            }
        })
        .catch(error => {
            console.error('[ARCHIVE] Load Error:', error);
            document.getElementById('categoryList').innerHTML = '<p>Error loading categories.</p>';
        });
    }

    function clearArchiveErrorMessage() {
        const elem = document.getElementById("archiveErrorMessage");
        elem.textContent = ''; 
        elem.style.display = 'none'; 
    }

    function closeArchiveModal() {
        document.getElementById('archiveCategoryModal').classList.remove('modal-active');
        clearArchiveErrorMessage();
    }

    function submitArchive() {
        const selectedCategories = [];
        const checkboxes = document.querySelectorAll('input[name="archive_category"]:checked');

        checkboxes.forEach(checkbox => {
            selectedCategories.push(checkbox.value);
        });

        const archiveErrorMessageContainer = document.getElementById("archiveErrorMessage");
        archiveErrorMessageContainer.textContent = ''; 
        archiveErrorMessageContainer.style.display = 'none'; 

        if (selectedCategories.length === 0) {
            archiveErrorMessageContainer.textContent = 'Please select at least one category to archive.';
            archiveErrorMessageContainer.style.display = 'block'; 
            return;
        }

        fetch('AdminChatbot.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Cache-Control': 'no-cache, no-store, must-revalidate'
            },
            body: JSON.stringify({
                action: 'archive', 
                categories: selectedCategories 
            }),
        })
        .then(response => {
            if (response.ok) {
                return response.json();
            } else {
                return response.json().then(data => { 
                    throw new Error(data.error); 
                });
            }
        })
        .then(data => {
            if (data.redirect) {
                closeArchiveModal();
                const timestamp = Date.now() + '-' + Math.random().toString(36).substr(2, 9);
                window.location.replace(`${window.location.pathname}?_=${timestamp}`);
            }
        })
        .catch(error => {
            console.error('[ARCHIVE] Submit Error:', error);
            archiveErrorMessageContainer.textContent = 'Error archiving categories: ' + error.message; 
            archiveErrorMessageContainer.style.display = 'block'; 
        });
    }

    // BULLETPROOF INITIALIZATION - ALWAYS LOADS ON ANY REFRESH/SWITCH
    let initAttempts = 0;
    const MAX_INIT_ATTEMPTS = 10;
    let lastInitTime = 0;
    let isUserEditing = false;

    // Detect if user is actively editing
    function detectUserActivity() {
        const editableElements = [
            document.getElementById('jsonDataCategory'),
            document.getElementById('jsonDataQuestions'),
            document.getElementById('jsonDataAnswers'),
            document.getElementById('newcategory'),
            document.getElementById('newquestions'),
            document.getElementById('newanswer')
        ];

        editableElements.forEach(elem => {
            if (elem) {
                elem.addEventListener('focus', () => {
                    isUserEditing = true;
                });
                elem.addEventListener('blur', () => {
                    setTimeout(() => {
                        isUserEditing = false;
                    }, 500);
                });
            }
        });
    }

    function forceInitialize() {
        // Don't initialize if user is actively editing
        if (isUserEditing) {
            return;
        }

        // Prevent too frequent initialization (debounce to 1 second)
        const now = Date.now();
        if (now - lastInitTime < 1000) {
            return;
        }
        lastInitTime = now;

        initAttempts++;

        const categorySelect = document.getElementById('category');
        if (!categorySelect) {
            if (initAttempts < MAX_INIT_ATTEMPTS) {
                setTimeout(forceInitialize, 50);
            }
            return;
        }
        
        // Always fetch categories - don't check if already loaded
        fetchCategories();
    }

    // Setup user activity detection
    setTimeout(detectUserActivity, 100);
    setTimeout(detectUserActivity, 500);

    // 1. Page Load Events
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', forceInitialize);
    } else {
        forceInitialize();
    }

    window.addEventListener('load', function() {
        forceInitialize();
    });

    // 2. Page Visibility - Critical for tab switching (but not if editing)
    document.addEventListener('visibilitychange', function() {
        if (!document.hidden && !isUserEditing) {
            forceInitialize();
        }
    });

    // 3. Focus Events - Critical for module switching (but not if editing)
    window.addEventListener('focus', function() {
        if (!isUserEditing) {
            forceInitialize();
        }
    });

    // 4. Page Show Event - Critical for back/forward and Ctrl+R
    window.addEventListener('pageshow', function(event) {
        forceInitialize();
    });

    // 5. Mutation Observer - Watch for DOM changes (disabled during editing)
    if (typeof MutationObserver !== 'undefined') {
        const observer = new MutationObserver(function(mutations) {
            if (!isUserEditing) {
                const categorySelect = document.getElementById('category');
                if (categorySelect) {
                    forceInitialize();
                }
            }
        });

        if (document.body) {
            observer.observe(document.body, {
                childList: true,
                subtree: true
            });
        } else {
            setTimeout(function() {
                if (document.body) {
                    observer.observe(document.body, {
                        childList: true,
                        subtree: true
                    });
                }
            }, 50);
        }
    }

    // 6. Delayed Safety Nets (only on initial load)
    setTimeout(forceInitialize, 100);
    setTimeout(forceInitialize, 300);
    setTimeout(forceInitialize, 500);

    // 7. Periodic Check - Only check if dropdown is empty and user isn't editing
    let periodicCheckCount = 0;
    const periodicCheck = setInterval(function() {
        periodicCheckCount++;
        if (!isUserEditing) {
            const select = document.getElementById('category');
            if (select && select.options.length <= 1) {
                forceInitialize();
            }
        }
        if (periodicCheckCount >= 5) {
            clearInterval(periodicCheck);
        }
    }, 2000);

    // 8. Bootstrap Tab Event (if using Bootstrap tabs)
    if (typeof $ !== 'undefined' && $.fn && $.fn.tab) {
        $(document).on('shown.bs.tab', function(e) {
            if (!isUserEditing) {
                const target = $(e.target).attr('href');
                forceInitialize();
            }
        });
    }

    // 9. Hash Change Event (for hash-based navigation)
    window.addEventListener('hashchange', function() {
        if (!isUserEditing) {
            forceInitialize();
        }
    });

    // 10. Storage Event (for cross-tab communication)
    window.addEventListener('storage', function(e) {
        if (e.key === 'chatbot_refresh' && !isUserEditing) {
            forceInitialize();
        }
    });

    // Clean up on unload
    window.addEventListener('beforeunload', function() {
        if (retryTimeout) {
            clearTimeout(retryTimeout);
        }
    });

    // Event Listeners Setup
    function setupEventListeners() {
        const elements = {
            category: document.getElementById('category'),
            jsonDataCategory: document.getElementById('jsonDataCategory'),
            jsonDataQuestions: document.getElementById('jsonDataQuestions'),
            jsonDataAnswers: document.getElementById('jsonDataAnswers'),
            saveChangesButton: document.getElementById('saveChangesButton'),
            newcategory: document.getElementById('newcategory'),
            newquestions: document.getElementById('newquestions'),
            newanswer: document.getElementById('newanswer')
        };

        if (elements.category) {
            elements.category.removeEventListener('change', loadCategoryData);
            elements.category.addEventListener('change', loadCategoryData);
        }
        
        if (elements.jsonDataCategory) {
            elements.jsonDataCategory.removeEventListener('input', toggleSaveButton);
            elements.jsonDataCategory.addEventListener('input', toggleSaveButton);
        }
        
        if (elements.jsonDataQuestions) {
            elements.jsonDataQuestions.removeEventListener('input', toggleSaveButton);
            elements.jsonDataQuestions.addEventListener('input', toggleSaveButton);
        }
        
        if (elements.jsonDataAnswers) {
            elements.jsonDataAnswers.removeEventListener('input', toggleSaveButton);
            elements.jsonDataAnswers.addEventListener('input', toggleSaveButton);
        }
        
        if (elements.saveChangesButton) {
            elements.saveChangesButton.removeEventListener('click', saveChanges);
            elements.saveChangesButton.addEventListener('click', saveChanges);
        }
        
        if (elements.newcategory) {
            const clearErrors = () => {
                document.getElementById("categoryErrorMessage1").style.display = 'none';
                document.getElementById("categoryErrorMessage").style.display = 'none';
                elements.newcategory.style.border = '';
            };
            elements.newcategory.removeEventListener('input', clearErrors);
            elements.newcategory.addEventListener('input', clearErrors);
        }
        
        if (elements.newquestions) {
            const clearBorder = (e) => e.target.style.border = '';
            elements.newquestions.removeEventListener('input', clearBorder);
            elements.newquestions.addEventListener('input', clearBorder);
        }
        
        if (elements.newanswer) {
            const clearBorder = (e) => e.target.style.border = '';
            elements.newanswer.removeEventListener('input', clearBorder);
            elements.newanswer.addEventListener('input', clearBorder);
        }
        
    }

    // Setup event listeners immediately and after delays
    setupEventListeners();
    setTimeout(setupEventListeners, 100);
    setTimeout(setupEventListeners, 500);

    // Handle success messages - Auto-fade after 3 seconds
    document.addEventListener("DOMContentLoaded", function () {
        const alerts = document.querySelectorAll(".warning");
        alerts.forEach(alert => {
            setTimeout(() => {
                alert.classList.add("fade");
                setTimeout(() => {
                    if (alert && alert.parentNode) {
                        alert.parentNode.removeChild(alert);
                    }
                }, 500); // allow fade-out
            }, 5000); // 3 seconds delay
        });
    });

</script>
</body>
</html>