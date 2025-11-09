<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Chatbot</title>
    <link rel="stylesheet" href="assets/css/Chatbotstyles.css">

</head>
<body>

        <div class="chat-icon" id="chatIcon" onclick="toggleChat(event)">
            <img src="assets/images/chatboticon.png" alt="Chat Icon">
        </div>
        <div class="chat-container" id="chatContainer" style="display: none">
            <div class="chatbox" id="chatbox"></div>

            <div class="suggestions" id="suggestions"></div>

            <div class="input-container">  
                <input type="text" id="userInput" class="user-input" placeholder="Type your message here..." onkeydown="handleKeyPress(event)">
                <button class="send-button" onclick="getResponse()">Send</button>
            </div>
        
        </div>

<script>

    // Function to toggle the Send button
        function toggleSendButton() {
            const userInput = document.getElementById('userInput').value;
            const sendButton = document.querySelector('.send-button');
        
            if (userInput.trim().length > 0) {
                sendButton.disabled = false;
                sendButton.style.backgroundColor = '#007BFF';
            } else {
                sendButton.disabled = true;
                sendButton.style.backgroundColor = '#aaa';
            };
        }
        document.getElementById('userInput').addEventListener('input', toggleSendButton);
        
        
        // Load questions-answers JSON
        fetch('./json files/questions-answers.json')
            .then(response => response.json())
            .then(data => {
                window.qaPairs = data;
                sendGreeting();
            })
            .catch(error => console.error('Error loading JSON:', error));
        
        let initialGreeting1Container = null;
        function sendGreeting() {
            const initialGreeting = "Hello! How can I assist you today?";
            const initialGreeting1 = "Here are some questions you might want to ask:";
        
            displayMessage(null, initialGreeting);
            initialGreeting1Container = displayMessage(null, initialGreeting1);
        
            generateSuggestions(Object.keys(window.qaPairs));
        }
        
        // Generate clickable question suggestions
        function generateSuggestions(suggestions) {
            const suggestionsContainer = document.getElementById('suggestions');
            suggestionsContainer.innerHTML = ''; // Clear previous suggestions
        
            const filteredSuggestions = suggestions.filter(
                key => key !== 'Greetings' && key !== 'Gratitude'
            );
        
            const shuffledSuggestions = filteredSuggestions.sort(() => Math.random() - 0.5);
            const limitedSuggestions = shuffledSuggestions.slice(0, 4); // Limit to 4 suggestions
        
            limitedSuggestions.forEach(key => {
                const button = document.createElement('button');
                button.className = 'suggestion-button';
                button.innerHTML = window.qaPairs[key].questions[0];
                button.onclick = () => handleSuggestionClick(button.innerHTML);

                button.onclick = (event) => {
                    event.stopPropagation(); 
                    handleSuggestionClick(button.innerHTML);
                };
                suggestionsContainer.appendChild(button);
            });
        }
        
        // Handle suggestion button click
        function handleSuggestionClick(buttonText) {
            clearSuggestions();
            displayMessage(buttonText, null); 
            handleSingleQuestion(buttonText.toLowerCase()); 
        }
        
        function getResponse() {
    const userInput = document.getElementById('userInput').value.trim().toLowerCase();
    if (!userInput) return;

    clearSuggestions(); // Clear suggestions when a message is sent
    displayMessage(userInput, null); // Display user input

    if (initialGreeting1Container) {
        initialGreeting1Container.remove();
        initialGreeting1Container = null;
    }

    // Check if input contains splitters (like 'and', ',', or '?') to determine multiple questions
    const hasSplitters = userInput.includes('and') || userInput.includes(',') || userInput.includes('?');

    if (hasSplitters) {
        handleMultipleQuestions(userInput); 
    } else {
        handleSingleQuestion(userInput); 
    }

    // Clear the input field
    document.getElementById('userInput').value = ''; 
    toggleSendButton(); 

    
}

        function clearSuggestions() {
            const suggestionsContainer = document.getElementById('suggestions');
            suggestionsContainer.innerHTML = ''; 
        }
        
        
        function handleSingleQuestion(question) {
    let response = "I'm not sure I understand. Could you clarify?";
    let recognized = false;

    question = question.toLowerCase();
    const gratitudeExpressions = window.qaPairs['Gratitude'].questions.map(q => q.toLowerCase());
    for (const gratitude of gratitudeExpressions) {
        if (question === gratitude) {
            response = window.qaPairs['Gratitude'].answer;
            recognized = true;
            break;
        }
    }

    const generalGreetings = window.qaPairs['Greetings'].questions.map(q => q.toLowerCase());
    for (const greeting of generalGreetings) {
        if (question === greeting) {
            response = window.qaPairs['Greetings'].answer;
            recognized = true;
            break;
        } else if (question.startsWith(greeting)) {
            question = question.replace(greeting, '').trim();
            break;
        }
    }

    const specificFacilities = ['Pool', 'Rooms', 'Pavilion', 'Renatos Hall', 'Mini Function Hall'];
    for (const facility of specificFacilities) {
        if (question.includes(facility)) {
            response = window.qaPairs[facility]?.answer || response;
            recognized = true;
            break;
        }
    }

    if (!recognized) {
        const specificKeywords = Object.keys(window.qaPairs);
        for (const key of specificKeywords) {
            if (window.qaPairs[key].questions.some(q => question.includes(q.toLowerCase()))) {
                response = window.qaPairs[key].answer;
                recognized = true;
                break;
            }
        }
    }

    if (!recognized) {
        response = "It seems there might be some confusion. Could you clarify what you need help with?";
        generateSuggestions(Object.keys(window.qaPairs));  
    }

    displayMessage(null, response);
}
        
        function handleMultipleQuestions(input) {
            const normalizedInput = input.replace(/[,.?]/g, ' and ').trim();
            const questions = normalizedInput.split(' and ').map(q => q.trim());
            let responses = [];

            questions.forEach(question => {
                let found = false;
                question = question.toLowerCase(); 

                const generalGreetings = window.qaPairs['Greetings'].questions.map(q => q.toLowerCase());
                for (const greeting of generalGreetings) {
                    if (question === greeting) {
                        responses.push(window.qaPairs['Greetings'].answer);
                        found = true;
                        break; 
                    } else if (question.startsWith(greeting)) {
                        question = question.replace(greeting, '').trim();
                        break; 
                    }
                }

                const specificKeywords = Object.keys(window.qaPairs);
                for (const key of specificKeywords) {
                    if (window.qaPairs[key].questions.some(q => question.includes(q.toLowerCase()))) {
                        responses.push(window.qaPairs[key].answer);
                        found = true;
                        break;
                    }
                }

                if (!found) {
                    responses.push(`I don't understand: "${question}".`);
                    generateSuggestions(Object.keys(window.qaPairs));
                }
            });

            responses.forEach(response => {
                displayMessage(null, response); 
            });
        }
 
        // Display messages in chatbox
        function displayMessage(userInput, response) {
            const chatbox = document.getElementById('chatbox');
            
            if (userInput) {
                const userMessageContainer = document.createElement('div');
                userMessageContainer.className = 'message-container';
                const userMessage = document.createElement('div');
                userMessage.className = 'message user-message';
                userMessage.innerHTML = userInput;
                userMessageContainer.appendChild(userMessage);
                chatbox.appendChild(userMessageContainer);
            }

            if (response) {
                const chatbotMessageContainer = document.createElement('div');
                chatbotMessageContainer.className = 'message-container';
                const chatbotMessage = document.createElement('div');
                chatbotMessage.className = 'message chatbot-message';
                // Replace both literal \n and actual newlines with <br>, and handle &nbsp;
                let formattedResponse = response
                    .replace(/\\n/g, '<br>')  // Handle literal \n
                    .replace(/\n/g, '<br>')   // Handle actual newlines
                    .replace(/&nbsp;/g, '&nbsp;');  // Preserve &nbsp; entities
                chatbotMessage.innerHTML = formattedResponse;
                chatbotMessageContainer.appendChild(chatbotMessage);
                chatbox.appendChild(chatbotMessageContainer);
                generateSuggestions(Object.keys(window.qaPairs));
            }

            document.getElementById('userInput').focus();
        }

        // Handle Enter key to send message
        function handleKeyPress(event) {
            if (event.key === 'Enter')
                getResponse();
        }

        let isChatOpen = false; // Track if chat is open
        let isToggling = false; // Prevent multiple toggles at once

// Ensure the chat container is initially hidden
window.addEventListener('load', () => {
    document.getElementById('chatContainer').style.display = 'none';
});

// Function to open or close the chat
function toggleChat(event) {
    if (isToggling) return;
    isToggling = true;

    if (event) event.stopPropagation();  

    if (isChatOpen) {
        closeChat();
    } else {
        openChat();
    }

    setTimeout(() => {
        isToggling = false; 
    }, 100); 
}

// Function to open the chat
function openChat() {
    const chatContainer = document.getElementById('chatContainer');
    chatContainer.style.display = 'block';
    isChatOpen = true;
    console.log('Chat is now open');

    setTimeout(() => {
        document.getElementById('userInput').focus(); // Focus on input field
    }, 100);
}

// Function to close the chat
function closeChat() {
    const chatContainer = document.getElementById('chatContainer');
    chatContainer.style.display = 'none';
    isChatOpen = false;
    console.log('Chat is now closed');
}

// Handle clicks outside the chat to close it
function handleClickOutside(event) {
    const chatContainer = document.getElementById('chatContainer');
    const chatIcon = document.getElementById('chatIcon');
    const suggestionsContainer = document.getElementById('suggestions');

    // Ensure the click is outside the chat container, chat icon, and suggestions container
    if (
        !chatContainer.contains(event.target) &&
        !chatIcon.contains(event.target) &&
        !suggestionsContainer.contains(event.target)
    ) {
        closeChat(); // Close the chat when clicking outside
    }
}

// Add event listener to close the chat on outside clicks
document.addEventListener('click', handleClickOutside);



document.addEventListener('DOMContentLoaded', () => {
    const userInput = document.getElementById('userInput');
    const sendButton = document.querySelector('.send-button');
    const chatIcon = document.getElementById('chatIcon');



    // Add event listeners
    if (userInput) userInput.addEventListener('keypress', handleKeyPress);
    if (sendButton) sendButton.addEventListener('click', getResponse);
    if (chatIcon) chatIcon.addEventListener('click', toggleChat); // Toggles chat open/close
});




</script>

</body>
</html>