<?php    ini_set('display_errors', 1);    error_reporting(E_ALL);

    require_once '../includes/validate.php';    
    require '../includes/name.php';    
    require_once 'log_activity.php';

if(!isset($_SESSION['role']) || !in_array($_SESSION['role'], ['Super Admin', 'Admin'])){
header("location:home.php");
exit();
}

    $content_file = '../admin/content_config.json';

    // Function to read content from JSON file
    function readContent($file, $is_archive = false) {
        clearstatcache(true, $file);
        if (file_exists($file) && is_readable($file)) {
            $json_content = file_get_contents($file);
            if ($json_content === false || empty($json_content)) {
                error_log("Failed to read content from $file or file is empty.");
                return $is_archive ? [] : getDefaultContentStructure();
            }
            $data = json_decode($json_content, true);
            if (json_last_error() !== JSON_ERROR_NONE) {
                error_log("JSON decoding error in $file: " . json_last_error_msg());
                return $is_archive ? [] : getDefaultContentStructure();
            }
            if ($is_archive) {
                return $data;
            }
            // Merge with default structure to ensure all keys exist
            return array_replace_recursive(getDefaultContentStructure(), $data);
        }
        error_log("Content file $file does not exist or is not readable.");
        return $is_archive ? [] : getDefaultContentStructure();
    }

    // Function to write content to JSON file
    function writeContent($file, $data) {
        file_put_contents($file, json_encode($data, JSON_PRETTY_PRINT));
    }

    // Function to provide a default, complete content structure
    function getDefaultContentStructure() {
        return [
            "about" => [
                "title" => "About Us",
                "content" => [
                    "Renato's Place Private Resort and Events is a premier destination for relaxation and celebrations. We offer a serene environment with top-notch facilities to make your events memorable and your stays comfortable."
                ],
                "mission" => [
                    "Our mission is to provide an unparalleled resort experience, combining luxurious comfort with exceptional service to create lasting memories for our guests."
                ],
                "vision" => [
                    "To be the leading private resort and events venue, recognized for our exquisite facilities, personalized service, and commitment to guest satisfaction."
                ],
                "image" => "about.jpg"
            ],
            "home" => [
                "slides" => [
                    ["image" => "home-slide5.jpg", "text" => "experience the balinese ambiance only at Renato's Place Private Resort and Events"],
                    ["image" => "home-slide2.jpg", "text" => "experience the balinese ambiance only at Renato's Place Private Resort and Events"],
                    ["image" => "home-slide3.jpg", "text" => "experience the balinese ambiance only at Renato's Place Private Resort and Events"],
                    ["image" => "home-slide4.jpg", "text" => "experience the balinese ambiance only at Renato's Place Private Resort and Events"]
                ],
                "offers" => [
                    ["image" => "offer1.jpg"],
                    ["image" => "offer2.jpg"],
                    ["image" => "offer3.jpg"],
                    ["image" => "offer4.jpg"],
                    ["image" => "offer5.jpg"],
                    ["image" => "offer6.jpg"],
                    ["image" => "offer7.jpg"]
                ],
                "services" => [
                    ["image" => "service1.png", "title" => "swimming pool"],
                    ["image" => "service2.png", "title" => "food & drinks"],
                    ["image" => "service3.png", "title" => "bar"]
                ],
                "affiliates" => [
                    ["image" => "catering.png", "title" => "Affiliate Caterers", "modal_id" => "modal1"],
                    ["image" => "supplier.png", "title" => "LIGHTS AND SOUND", "modal_id" => "modal2"],
                    ["image" => "service3.png", "title" => "Bar", "modal_id" => "modal3"],
                    ["image" => "dessert icon.png", "title" => "Grazing Table", "modal_id" => "modal4"]
                ],
                "affiliate_modals" => [
                    "modal1" => [
                        "title" => "Affiliate Caterers",
                        "content" => [
                            ["name" => "DENSOL'S CATERING", "facebook" => "https://www.facebook.com/densolscatering", "contacts" => ["09656051741", "09983941602", "09361732001", "02-82869256"]],
                            ["name" => "CHEF MARIA'S CATERING", "facebook" => "https://www.facebook.com/chefmariascatering", "contacts" => ["09285211340"]],
                            ["name" => "MADRIAGA CATERING", "facebook" => "https://www.facebook.com/madriagacatering", "contacts" => ["09109477722"]],
                            ["name" => "SAC-B CATERING", "facebook" => "https://www.facebook.com/sacbcateringservice", "contacts" => ["279589260"]],
                            ["name" => "HIZON'S CATERING", "facebook" => "https://www.facebook.com/weddingcateringbyhizons?mibextid=ZbWKwL", "contacts" => ["09665046540"]]
                        ]
                    ],
                    "modal2" => [
                        "title" => "LIGHTS AND SOUND",
                        "content" => [
                            ["name" => "Orange and string studio", "facebook" => "https://www.facebook.com/orangeandstringsstudio?mibextid=ZbWKwL", "contacts" => ["09194184035"]],
                            ["name" => "JGR", "facebook" => "https://www.facebook.com/profile.php?id=100064086667821&mibextid=ZbWKwL", "contacts" => ["09157964743"]],
                            ["name" => "Rave", "facebook" => "https://www.facebook.com/profile.php?id=100083036905420&mibextid=ZbWKwL", "contacts" => ["09455141763"]]
                        ]
                    ],
                    "modal3" => [
                        "title" => "Bar",
                        "content" => [
                            ["name" => "Renato's Cafe", "facebook" => "https://www.facebook.com/Renatoscafefoodandbar?mibextid=ZbWKwL", "contacts" => ["09610203503"]]
                        ]
                    ],
                    "modal4" => [
                        "title" => "Grazing Table",
                        "content" => [
                            ["name" => "Donut wall by hazel", "facebook" => "https://www.facebook.com/donutwallbyhazel?mibextid=ZbWKwL", "contacts" => ["09056671086"]],
                            ["name" => "The Yolk Coffee and Snack Bar", "facebook" => "https://www.facebook.com/TheYolkk?mibextid=ZbWKwL", "contacts" => ["09557496398"]],
                            ["name" => "(Koun Takoyaki)", "facebook" => "https://www.facebook.com/kountakoyaki?mibextid=ZbWKwL", "contacts" => ["09152356078"]]
                        ]
                    ]
                ]
            ],
            "rooms" => [
                "heading" => "Our Rooms",
                "list" => [
                    [
                        "img" => "assets/images/room1.jpg",
                        "title" => "Suite Room (22 Hours)",
                        "desc" => "Experience ultimate comfort with full kitchen amenities and a spacious environment.",
                        "features" => ["2 Rooms", "1 Bathroom", "AC", "WiFi", "TV", "Max 6 Guests"],
                        "price" => "PHP 3,500.00"
                    ],
                    [
                        "img" => "assets/images/room3.jpg",
                        "title" => "Suite Room (12 Hours)",
                        "desc" => "A cozy and comfortable stay, perfect for a relaxing getaway or a productive work session.",
                        "features" => ["2 Rooms", "1 Bathroom", "AC", "WiFi", "TV", "Max 6 Guests"],
                        "price" => "PHP 2,500.00"
                    ]
                ]
            ],
            "venues" => [
                "heading" => "Our Venues",
                "list" => [
                    [
                        "img" => "assets/images/garden1.jpg",
                        "title" => "Garden",
                        "desc" => "A beautiful outdoor space perfect for weddings and special occasions, surrounded by lush greenery.",
                        "modal_content" => "Details for Garden Venue."
                    ],
                    [
                        "img" => "assets/images/MiniF1.jpg",
                        "title" => "Mini Function Hall",
                        "desc" => "An intimate and elegant hall ideal for debuts, parties, and corporate events.",
                        "modal_content" => "Details for Mini Function Hall."
                    ],
                    [
                        "img" => "assets/images/pavilion1.jpg",
                        "title" => "Pavilion",
                        "desc" => "A versatile and spacious pavilion suitable for large gatherings and celebrations.",
                        "modal_content" => "Details for Pavilion."
                    ],
                    [
                        "img" => "assets/images/hall1.jpg",
                        "title" => "Renato's Hall",
                        "desc" => "Our grand hall, perfect for making a statement with its luxurious ambiance and ample space.",
                        "modal_content" => "Details for Renato's Hall."
                    ]
                ]
            ],
            "gallery" => [
                "heading" => "Our Gallery",
                "images_dir" => "assets/image/gallery/",
                "images" => [
                ]
            ]
        ];
    }

    $content_data = readContent($content_file);
    $message = '';
    if (isset($_SESSION['success_message'])) {
        $message = '<div class="warning alert-success" 
            style="font-size:13px; padding:18px; margin:20px 0; text-align:left; font-weight:bold; border-radius:6px;">'
            . htmlspecialchars($_SESSION['success_message']) . 
        '</div>';
        unset($_SESSION['success_message']);
    }

    // Helper function to handle image uploads from array-based file inputs
    function handleImageUpload(&$item, $file_input_name, $index, $upload_dir, &$message, $image_key = 'image') {
        if (isset($_FILES[$file_input_name]['name'][$index]) && $_FILES[$file_input_name]['error'][$index] === UPLOAD_ERR_OK) {
            $file_name = basename($_FILES[$file_input_name]['name'][$index]);
            $file_name = preg_replace('/[^a-zA-Z0-9._-]/', '_', $file_name);
            $target_file = $upload_dir . $file_name;
            $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));
            $tmp_name = $_FILES[$file_input_name]['tmp_name'][$index];

            // Check if image file is a actual image or fake image
            $check = getimagesize($tmp_name);
            if($check === false) {
                $message .= "File is not an image for {$file_input_name}[{$index}]. ";
                return;
            }

            // Allow certain file formats
            $allowed_types = ['jpg', 'jpeg', 'png', 'gif'];
            if (!in_array($imageFileType, $allowed_types)) {
                $message .= "Sorry, only JPG, JPEG, PNG & GIF files are allowed for {$file_input_name}[{$index}]. ";
                return;
            }

            if (move_uploaded_file($tmp_name, $target_file)) {
                $item[$image_key] = $file_name;
            } else {
                $message .= "Sorry, there was an error uploading your file for {$file_input_name}[{$index}]. ";
                error_log("Failed to upload file: " . $file_name);
            }
        } elseif (isset($_FILES[$file_input_name]['error'][$index]) && $_FILES[$file_input_name]['error'][$index] !== UPLOAD_ERR_NO_FILE) {
            $message .= "File upload error for {$file_input_name}[{$index}]: " . $_FILES[$file_input_name]['error'][$index] . ". ";
        }
    }

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $current_section_message = '';
        if (isset($_POST['section'])) {
            switch ($_POST['section']) {
                case 'archive_gallery_image':
                    $filename = $_POST['filename'];
                    $content_archive_file = '../admin/content_config_archive.json';
                    $archived_content = readContent($content_archive_file, true);

                    if (!isset($archived_content['gallery']['images'])) {
                        $archived_content['gallery']['images'] = [];
                    }

                    foreach ($content_data['gallery']['images'] as $key => $image) {
                        if ($image['filename'] === $filename) {
                            $archived_content['gallery']['images'][] = $image;
                            unset($content_data['gallery']['images'][$key]);
                            break;
                        }
                    }

                    $content_data['gallery']['images'] = array_values($content_data['gallery']['images']);

                    writeContent($content_file, $content_data);
                    writeContent($content_archive_file, $archived_content);
                    log_activity($_SESSION['admin_id'], 'Content Management', 'Archived a gallery image.');
                    break;

                case 'archive_venue':
                    $index = $_POST['index'];
                    $content_archive_file = '../admin/content_config_archive.json';
                    $archived_content = readContent($content_archive_file, true);

                    if (!isset($archived_content['venues']['list'])) {
                        $archived_content['venues']['list'] = [];
                    }

                    $archived_content['venues']['list'][] = $content_data['venues']['list'][$index];
                    unset($content_data['venues']['list'][$index]);
                    $content_data['venues']['list'] = array_values($content_data['venues']['list']);

                    writeContent($content_file, $content_data);
                    writeContent($content_archive_file, $archived_content);
                    log_activity($_SESSION['admin_id'], 'Content Management', 'Archived a venue.');
                    break;

                case 'archive_room':
                    $index = $_POST['index'];
                    $content_archive_file = '../admin/content_config_archive.json';
                    $archived_content = readContent($content_archive_file, true);

                    if (!isset($archived_content['rooms']['list'])) {
                        $archived_content['rooms']['list'] = [];
                    }

                    $archived_content['rooms']['list'][] = $content_data['rooms']['list'][$index];
                    unset($content_data['rooms']['list'][$index]);
                    $content_data['rooms']['list'] = array_values($content_data['rooms']['list']);

                    writeContent($content_file, $content_data);
                    writeContent($content_archive_file, $archived_content);
                    log_activity($_SESSION['admin_id'], 'Content Management', 'Archived a room.');
                    break;

                case 'archive_affiliate_modal':
                    $modal_id = $_POST['modal_id'];
                    $content_archive_file = '../admin/content_config_archive.json';
                    $archived_content = readContent($content_archive_file, true);

                    if (!isset($archived_content['home']['affiliate_modals'])) {
                        $archived_content['home']['affiliate_modals'] = [];
                    }

                    $archived_content['home']['affiliate_modals'][$modal_id] = $content_data['home']['affiliate_modals'][$modal_id];
                    unset($content_data['home']['affiliate_modals'][$modal_id]);

                    writeContent($content_file, $content_data);
                    writeContent($content_archive_file, $archived_content);
                    log_activity($_SESSION['admin_id'], 'Content Management', 'Archived an affiliate modal.');
                    break;

                case 'archive_affiliate':
                    $index = $_POST['index'];
                    $content_archive_file = '../admin/content_config_archive.json';
                    $archived_content = readContent($content_archive_file, true);

                    if (!isset($archived_content['home']['affiliates'])) {
                        $archived_content['home']['affiliates'] = [];
                    }

                    $archived_content['home']['affiliates'][] = $content_data['home']['affiliates'][$index];
                    unset($content_data['home']['affiliates'][$index]);
                    $content_data['home']['affiliates'] = array_values($content_data['home']['affiliates']);

                    writeContent($content_file, $content_data);
                    writeContent($content_archive_file, $archived_content);
                    log_activity($_SESSION['admin_id'], 'Content Management', 'Archived an affiliate.');
                    break;

                case 'archive_service':
                    $index = $_POST['index'];
                    $content_archive_file = '../admin/content_config_archive.json';
                    $archived_content = readContent($content_archive_file, true);

                    if (!isset($archived_content['home']['services'])) {
                        $archived_content['home']['services'] = [];
                    }

                    $archived_content['home']['services'][] = $content_data['home']['services'][$index];
                    unset($content_data['home']['services'][$index]);
                    $content_data['home']['services'] = array_values($content_data['home']['services']);

                    writeContent($content_file, $content_data);
                    writeContent($content_archive_file, $archived_content);
                    log_activity($_SESSION['admin_id'], 'Content Management', 'Archived a service.');
                    break;

                case 'archive_offer':
                    $index = $_POST['index'];
                    $content_archive_file = '../admin/content_config_archive.json';
                    $archived_content = readContent($content_archive_file, true);

                    if (!isset($archived_content['home']['offers'])) {
                        $archived_content['home']['offers'] = [];
                    }

                    $archived_content['home']['offers'][] = $content_data['home']['offers'][$index];
                    unset($content_data['home']['offers'][$index]);
                    $content_data['home']['offers'] = array_values($content_data['home']['offers']);

                    writeContent($content_file, $content_data);
                    writeContent($content_archive_file, $archived_content);
                    log_activity($_SESSION['admin_id'], 'Content Management', 'Archived an offer.');
                    break;

                case 'archive_slide':
                    $index = $_POST['index'];
                    $content_archive_file = '../admin/content_config_archive.json';
                    $archived_content = readContent($content_archive_file, true);

                    if (!isset($archived_content['home']['slides'])) {
                        $archived_content['home']['slides'] = [];
                    }

                    $archived_content['home']['slides'][] = $content_data['home']['slides'][$index];
                    unset($content_data['home']['slides'][$index]);
                    $content_data['home']['slides'] = array_values($content_data['home']['slides']);

                    writeContent($content_file, $content_data);
                    writeContent($content_archive_file, $archived_content);
                    log_activity($_SESSION['admin_id'], 'Content Management', 'Archived a slide.');
                    break;

                case 'about':
                    $content_data['about']['content'] = array_map('trim', explode('    
', $_POST['about_content']));
                    $content_data['about']['mission'] = array_map('trim', explode('    
', $_POST['mission_content']));
                    $content_data['about']['vision'] = array_map('trim', explode('    
', $_POST['vision_content']));
                    $current_section_message = 'About Page content updated successfully!';
                    log_activity($_SESSION['admin_id'], 'Content Management', 'Updated About Us page content.');
                    break;
                case 'home_slides':
                    $upload_dir = '../assets/images/';
                    if (isset($_POST['home_slides']) && is_array($_POST['home_slides'])) {
                        $new_slides = [];
                        foreach ($_POST['home_slides'] as $index => $slide) {
                            $new_slide = [
                                'image' => $slide['image'] ?? '',
                                'text' => $slide['text'] ?? ''
                            ];
                            handleImageUpload($new_slide, 'home_slides_file', $index, $upload_dir, $current_section_message);
                            $new_slides[] = $new_slide;
                        }
                        $content_data['home']['slides'] = $new_slides;
                    }
                    if(empty($current_section_message)) {
                        $current_section_message = 'Home Page Slides updated successfully!';
                    }
                    log_activity($_SESSION['admin_id'], 'Content Management', 'Updated Home Page Slides.');
                    break;

                case 'home_offers':
                    $upload_dir = '../assets/images/';
                    if (isset($_POST['home_offers']) && is_array($_POST['home_offers'])) {
                        $new_offers = [];
                        foreach ($_POST['home_offers'] as $index => $offer) {
                            $new_offer = [
                                'image' => $offer['image'] ?? ''
                            ];
                            handleImageUpload($new_offer, 'home_offers_file', $index, $upload_dir, $current_section_message);
                            $new_offers[] = $new_offer;
                        }
                        $content_data['home']['offers'] = $new_offers;
                    }
                    if(empty($current_section_message)) {
                        $current_section_message = 'Home Page Offers updated successfully!';
                    }
                    log_activity($_SESSION['admin_id'], 'Content Management', 'Updated Home Page Offers.');
                    break;

                case 'home_services':
                    $upload_dir = '../assets/images/';
                    if (isset($_POST['home_services']) && is_array($_POST['home_services'])) {
                        $new_services = [];
                        foreach ($_POST['home_services'] as $index => $service) {
                            $new_service = [
                                'image' => $service['image'] ?? '',
                                'title' => $service['title'] ?? ''
                            ];
                            handleImageUpload($new_service, 'home_services_file', $index, $upload_dir, $current_section_message);
                            $new_services[] = $new_service;
                        }
                        $content_data['home']['services'] = $new_services;
                    }
                    if(empty($current_section_message)) {
                        $current_section_message = 'Home Page Services updated successfully!';
                    }
                    log_activity($_SESSION['admin_id'], 'Content Management', 'Updated Home Page Services.');
                    break;

                case 'home_affiliates':
                    $upload_dir = '../assets/images/';
                    if (isset($_POST['home_affiliates']) && is_array($_POST['home_affiliates'])) {
                        $new_affiliates = [];
                        foreach ($_POST['home_affiliates'] as $index => $affiliate) {
                            $new_affiliate = [
                                'image' => $affiliate['image'] ?? '',
                                'title' => $affiliate['title'] ?? '',
                                'modal_id' => $affiliate['modal_id'] ?? ''
                            ];
                            handleImageUpload($new_affiliate, 'home_affiliates_file', $index, $upload_dir, $current_section_message);
                            $new_affiliates[] = $new_affiliate;
                        }
                        $content_data['home']['affiliates'] = $new_affiliates;
                    }
                    if(empty($current_section_message)) {
                        $current_section_message = 'Home Page Affiliates updated successfully!';
                    }
                    log_activity($_SESSION['admin_id'], 'Content Management', 'Updated Home Page Affiliates.');
                    break;

                case 'home_affiliate_modals':
                    if (isset($_POST['home_affiliate_modals']) && is_array($_POST['home_affiliate_modals'])) {
                        $new_affiliate_modals = [];
                        foreach ($_POST['home_affiliate_modals'] as $modal_id => $modal_data) {
                            $new_modal_content = [];
                            if (isset($modal_data['content']) && is_array($modal_data['content'])) {
                                foreach ($modal_data['content'] as $item_index => $item) {
                                    $new_modal_content[] = [
                                        'name' => $item['name'] ?? '',
                                        'facebook' => $item['facebook'] ?? '',
                                        'contacts' => isset($item['contacts']) ? array_map('trim', explode(',', $item['contacts'])) : []
                                    ];
                                }
                            }
                            $new_affiliate_modals[$modal_id] = [
                                'title' => $modal_data['title'] ?? '',
                                'content' => $new_modal_content
                            ];
                        }
                        $content_data['home']['affiliate_modals'] = $new_affiliate_modals;
                    }
                    if(empty($current_section_message)) {
                        $current_section_message = 'Home Page Affiliate Modals updated successfully!';
                    }
                    log_activity($_SESSION['admin_id'], 'Content Management', 'Updated Home Page Affiliate Modals.');
                    break;
                case 'rooms':
                    $upload_dir = '../assets/images/'; // Directory to save uploaded images

                    if (isset($_POST['save_item'])) {
                        $index = $_POST['save_item'];
                        if (isset($_POST['rooms_list'][$index])) {
                            $room_data = $_POST['rooms_list'][$index];
                            $content_data['rooms']['list'][$index]['title'] = $room_data['title'] ?? '';
                            $content_data['rooms']['list'][$index]['desc'] = $room_data['desc'] ?? '';
                            $content_data['rooms']['list'][$index]['features'] = isset($room_data['features']) ? array_map('trim', explode(',', $room_data['features'])) : [];
                            $content_data['rooms']['list'][$index]['price'] = $room_data['price'] ?? '';

                            handleImageUpload($content_data['rooms']['list'][$index], 'rooms_list_file', $index, $upload_dir, $current_section_message, 'img');
                            $current_section_message = 'Room #' . ($index + 1) . ' updated successfully!';
                            log_activity($_SESSION['admin_id'], 'Content Management', 'Updated room #' . $index);
                        }
                    } else {
                        if (isset($_POST['rooms_list']) && is_array($_POST['rooms_list'])) {
                            $new_rooms = [];
                            foreach ($_POST['rooms_list'] as $index => $room) {
                                $new_room = [
                                    'img' => $room['img'] ?? '',
                                    'title' => $room['title'] ?? '',
                                    'desc' => $room['desc'] ?? '',
                                    'features' => isset($room['features']) ? array_map('trim', explode(',', $room['features'])) : [],
                                    'price' => $room['price'] ?? ''
                                ];
                                handleImageUpload($new_room, 'rooms_list_file', $index, $upload_dir, $current_section_message, 'img');
                                $new_rooms[] = $new_room;
                            }
                            $content_data['rooms']['list'] = $new_rooms;
                        }
                        if(empty($current_section_message)) {
                            $current_section_message = 'Rooms Page content updated successfully!';
                        }
                        log_activity($_SESSION['admin_id'], 'Content Management', 'Updated Rooms page content.');
                    }
                    break;
case 'venues':
    $upload_dir = '../assets/images/'; // Directory to save uploaded images
    if (isset($_POST['save_item'])) {
        $index = $_POST['save_item'];
        if (isset($_POST['venues_list'][$index])) {
            $venue_data = $_POST['venues_list'][$index];
            $content_data['venues']['list'][$index]['title'] = $venue_data['title'] ?? '';
            // Preserve line breaks and spaces for description
            $content_data['venues']['list'][$index]['desc'] = $venue_data['desc'] ?? '';
            // Preserve line breaks and spaces for modal content
            $content_data['venues']['list'][$index]['modal_content'] = $venue_data['modal_content'] ?? '';

            handleImageUpload($content_data['venues']['list'][$index], 'venues_list_file', $index, $upload_dir, $current_section_message, 'img');
            $current_section_message = 'Venue #' . ($index + 1) . ' updated successfully!';
            log_activity($_SESSION['admin_id'], 'Content Management', 'Updated venue #' . $index);
        }
    } else {
        if (isset($_POST['venues_list']) && is_array($_POST['venues_list'])) {
            $new_venues = [];
            foreach ($_POST['venues_list'] as $index => $venue) {
                $new_venue = [
                    'img' => $venue['img'] ?? '',
                    'title' => $venue['title'] ?? '',
                    // Preserve line breaks and spaces for description
                    'desc' => $venue['desc'] ?? '',
                    // Preserve line breaks and spaces for modal content
                    'modal_content' => $venue['modal_content'] ?? ''
                ];
                handleImageUpload($new_venue, 'venues_list_file', $index, $upload_dir, $current_section_message, 'img');
                $new_venues[] = $new_venue;
            }
            $content_data['venues']['list'] = $new_venues;
        }
        if(empty($current_section_message)) {
            $current_section_message = 'Venues Page content updated successfully!';
        }
        log_activity($_SESSION['admin_id'], 'Content Management', 'Updated Venues page content.');
    }
    break;;
                case 'gallery':
                    if (isset($_POST['save_item'])) {
                        $index = $_POST['save_item'];
                        if (isset($_POST['gallery_images'][$index])) {
                            $image_data = $_POST['gallery_images'][$index];
                            $content_data['gallery']['images'][$index]['caption'] = $image_data['caption'] ?? '';
                            $content_data['gallery']['images'][$index]['category'] = $image_data['category'] ?? '';
                            $current_section_message = 'Gallery item #' . ($index + 1) . ' updated successfully!';
                            log_activity($_SESSION['admin_id'], 'Content Management', 'Updated gallery item #' . $index);
                        }
                    } else {
                        // Handle existing image updates (global save)
                        if (isset($_POST['gallery_images']) && is_array($_POST['gallery_images'])) {
                            $updated_images = [];
                            foreach ($_POST['gallery_images'] as $image_data) {
                                if (isset($image_data['filename'], $image_data['caption'], $image_data['category'])) {
                                    $updated_images[] = [
                                        'filename' => $image_data['filename'],
                                        'caption' => $image_data['caption'],
                                        'category' => $image_data['category']
                                    ];
                                }
                            }
                            $content_data['gallery']['images'] = $updated_images;
                            $current_section_message = 'Gallery content updated successfully!';
                            log_activity($_SESSION['admin_id'], 'Content Management', 'Updated existing gallery content.');
                        }

                        // Handle new image upload
                        if (isset($_FILES['new_gallery_image']) && $_FILES['new_gallery_image']['error'] === UPLOAD_ERR_OK) {
                            $upload_dir = '../assets/images/admin/gallery/';
                            $file_name = basename($_FILES['new_gallery_image']['name']);
                            $target_file = $upload_dir . $file_name;

                            if (!is_dir($upload_dir)) {
                                mkdir($upload_dir, 0755, true);
                            }

                            if (move_uploaded_file($_FILES['new_gallery_image']['tmp_name'], $target_file)) {
                                $new_image = [
                                    'filename' => $file_name,
                                    'caption' => $_POST['new_gallery_caption'] ?? '',
                                    'category' => $_POST['new_gallery_category'] ?? ''
                                ];
                                $content_data['gallery']['images'][] = $new_image;
                                $current_section_message = 'Gallery image uploaded and content updated successfully!';
                                log_activity($_SESSION['admin_id'], 'Content Management', 'Uploaded new gallery image: ' . $file_name);
                            } else {
                                $current_section_message = 'Failed to upload gallery image.';
                            }
                        } elseif (isset($_FILES['new_gallery_image']) && $_FILES['new_gallery_image']['error'] !== UPLOAD_ERR_NO_FILE) {
                            $current_section_message = 'File upload error: ' . $_FILES['new_gallery_image']['error'] . '.';
                        }
                    }
                    break;
            }
            writeContent($content_file, $content_data);
            if (!empty($current_section_message)) {
                $_SESSION['success_message'] = $current_section_message;
                $tab = $_POST['section'] ?? 'about';
                header("Location: manage_content.php#$tab");
                exit();
            }
        }
    }
?>
<!DOCTYPE html>
<html lang="en">
<head>
        <title>Renato's Place Private Resort and Events</title>

    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" type="text/css" href="../assets/css/admin/bootstrap.css" />
    <link rel="stylesheet" type="text/css" href="../assets/css/admin/style.css" />
    <link rel="stylesheet" type="text/css" href="../assets/css/admin/panel.css" />
    <link rel="icon" href="../assets/favicon.ico">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css">
</head>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/css/bootstrap.min.css">
<body>
    <div class="wrapper">
    <!-- Sidebar -->
    <nav id="sidebar">
        <div class="sidebar-header"><h3>Renato's Place</h3></div>
        <ul class="list-unstyled components">
            <li><a href="home.php">Dashboard</a></li>
            <li><a href="reserve.php">Reservation</a></li>
            <li><a href="inventory.php">Inventory</a></li>
            <?php if ($_SESSION['role'] == 'Super Admin') { ?>
            <li><a href="SalesRecord.php">Sales Record</a></li>
            <li><a href="settings.php">Settings</a></li>
            <li><a href="Allarchive.php">Archive</a></li>
            <?php } ?>
        </ul>
    </nav>

    <!-- Page Content -->
    <div id="content">
        <!-- Top Navbar -->
        <nav class="navbar navbar-default">
            <div class="container-fluid">
                <div class="navbar-header">
                    <button type="button" id="sidebarCollapse" class="btn btn-info navbar-btn">
                        <i class="glyphicon glyphicon-align-left"></i> Menu
                    </button>
                </div>
                <ul class="nav navbar-nav navbar-right">
                    <li class="dropdown">
                        <a class="dropdown-toggle" data-toggle="dropdown">
                            <i class="glyphicon glyphicon-user"></i> <?php echo $name; ?>
                        </a>
                        <ul class="dropdown-menu">
                            <li><a href="logout.php"><i class="glyphicon glyphicon-off"></i> Logout</a></li>
                        </ul>
                    </li>
                </ul>
            </div>
        </nav>
    <br />
    <div class="container-fluid">
        <div class="panel panel-default">
            <div class="panel-body">
                <div class="alert alert-info">Manage Website Content</div>
                <a class = "btn btn-primary" href = "settings.php"><i class = "glyphicon glyphicon-arrow-left"></i> Back to Settings</a>
                <br>
                <br>
                <?php echo $message; ?>
                <ul class="nav nav-tabs" id="contentTabs" role="tablist">
                    <li class="active">
                        <a id="about-tab" data-toggle="tab" href="#about" role="tab" aria-controls="about" aria-selected="true">About Us</a>
                    </li>
                    <li>
                        <a id="home-tab" data-toggle="tab" href="#home" role="tab" aria-controls="home" aria-selected="false">Home Page</a>
                    </li>
                    <li>
                        <a id="rooms-tab" data-toggle="tab" href="#rooms" role="tab" aria-controls="rooms" aria-selected="false">Rooms</a>
                    </li>
                    <li>
                        <a id="venues-tab" data-toggle="tab" href="#venues" role="tab" aria-controls="venues" aria-selected="false">Venues</a>
                    </li>
                    <li>
                        <a id="gallery-tab" data-toggle="tab" href="#gallery" role="tab" aria-controls="gallery" aria-selected="false">Gallery</a>
                    </li>
                    <li>
                        <a id="prices-tab" data-toggle="tab" href="#prices" role="tab" aria-controls="prices" aria-selected="false">Prices</a>
                    </li>
                    <li>
                        <a id="chatbot-tab" data-toggle="tab" href="#chatbot" role="tab" aria-controls="chatbot" aria-selected="false">Chatbot Contents</a>
                    </li>

                </ul>
                <div class="tab-content" id="contentTabsContent">

                <div class="tab-pane fade" id="prices" role="tabpanel" aria-labelledby="prices-tab">
                    <div id="prices-content">Loading...</div>
                </div>

                <div class="tab-pane fade" id="chatbot" role="tabpanel" aria-labelledby="chatbot-tab">
                    <div id="chatbot-content">Loading...</div>
                </div>


                    <!-- About Us Tab Content -->
                    <div class="tab-pane fade in active" id="about" role="tabpanel" aria-labelledby="about-tab">
                        <br>
                        <form method="POST" action="">
                            <input type="hidden" name="section" value="about">
                            <div class="form-group">
                                <label for="about_title">About Us Title:</label>
                                <input type="text" class="form-control" id="about_title" name="about_title" value="<?php echo htmlspecialchars($content_data['about']['title']); ?>" readonly>
                            </div>
                            <div class="form-group">
                                <label for="about_content">About Us Content:</label>
                                <textarea class="form-control" id="about_content" name="about_content" rows="5"><?php echo htmlspecialchars(implode("\n", $content_data['about']['content'])); ?></textarea>
                            </div>
                            <div class="form-group">
                                <label for="mission_content">Mission Content:</label>
                                <textarea class="form-control" id="mission_content" name="mission_content" rows="5"><?php echo htmlspecialchars(implode("\n", $content_data['about']['mission'])); ?></textarea>
                            </div>
                            <div class="form-group">
                                <label for="vision_content">Vision Content:</label>
                                <textarea class="form-control" id="vision_content" name="vision_content" rows="5"><?php echo htmlspecialchars(implode("\n", $content_data['about']['vision'])); ?></textarea>
                            </div>
                            <button type="submit" class="btn btn-primary">Save About Us Content</button>
                        </form>
                    </div>

                    <!-- Home Page Tab Content -->
                    <div class="tab-pane fade" id="home" role="tabpanel" aria-labelledby="home-tab">
                        <br>
                        <h3>Home Page Content Management</h3>

                        <form method="POST" action="" enctype="multipart/form-data">
                            <input type="hidden" name="section" value="home_slides">
                            <h4>Slides</h4>
                            <div id="home_slides_container">
                                <?php foreach ($content_data['home']['slides'] as $index => $slide): ?>
                                    <div class="form-group slide-item" id="slide-<?php echo $index; ?>">
                                        <label>Slide <?php echo $index + 1; ?></label>
                                        <input type="text" class="form-control" name="home_slides[<?php echo $index; ?>][text]" value="<?php echo htmlspecialchars($slide['text']); ?>" placeholder="Slide Text">
                                        <br>
                                        <input type="hidden" name="home_slides[<?php echo $index; ?>][image]" value="<?php echo htmlspecialchars($slide['image']); ?>">
                                        <img src="../assets/images/<?php echo htmlspecialchars($slide['image']); ?>" alt="Slide Image" style="max-width: 100px; max-height: 100px;">
                                        <br>
                                        <br>
                                        <label class="btn btn-primary" style="display:block; width:100px; background-color:#337ab7; color:white; border-radius:4px; padding: 6px 12px; text-align:center; cursor:pointer;">Choose File<input type="file" name="home_slides_file[<?php echo $index; ?>]" accept="image/*" style="display:none;"></label>
                                        <br>
                                        <button type="button" class="btn btn-warning btn-sm archive-slide" data-index="<?php echo $index; ?>">Archive</button>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                            <button type="button" class="btn btn-success btn-sm" id="add_slide">Add New Slide</button>
                            <button type="submit" class="btn btn-primary btn-sm">Save Slides</button>
                            <hr>
                        </form>

                        <form method="POST" action="" enctype="multipart/form-data">
                            <input type="hidden" name="section" value="home_offers">
                            <h4>Offers</h4>
                            <div id="home_offers_container">
                                <?php foreach ($content_data['home']['offers'] as $index => $offer): ?>
                                    <div class="form-group offer-item" id="offer-<?php echo $index; ?>">
                                        <label>Offer <?php echo $index + 1; ?></label>
                                        <input type="hidden" name="home_offers[<?php echo $index; ?>][image]" value="<?php echo htmlspecialchars($offer['image']); ?>">
                                        <img src="../assets/images/<?php echo htmlspecialchars($offer['image']); ?>" alt="Offer Image" style="max-width: 150px; max-height: 150px;">
                                        <br>
                                        <br>
                                        <label class="btn btn-primary" style="display:block; width:100px; background-color:#337ab7; color:white; border-radius:4px; padding: 6px 12px; text-align:center; cursor:pointer;">Choose File<input type="file" name="home_offers_file[<?php echo $index; ?>]" accept="image/*" style="display:none;"></label>
                                        <br>
                                        <button type="button" class="btn btn-warning btn-sm archive-offer" data-index="<?php echo $index; ?>">Archive</button>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                            <button type="button" class="btn btn-success btn-sm" id="add_offer">Add New Offer</button>
                            <button type="submit" class="btn btn-primary btn-sm">Save Offers</button>
                            <hr>
                        </form>

                        <form method="POST" action="" enctype="multipart/form-data">
                            <input type="hidden" name="section" value="home_services">
                            <h4>Services</h4>
                            <div id="home_services_container">
                                <?php foreach ($content_data['home']['services'] as $index => $service): ?>
                                    <div class="form-group service-item" id="service-<?php echo $index; ?>">
                                        <label>Service <?php echo $index + 1; ?></label>
                                        <input type="text" class="form-control" name="home_services[<?php echo $index; ?>][title]" value="<?php echo htmlspecialchars($service['title']); ?>" placeholder="Service Title">
                                        <input type="hidden" name="home_services[<?php echo $index; ?>][image]" value="<?php echo htmlspecialchars($service['image']); ?>">
                                        <img src="../assets/images/<?php echo htmlspecialchars($service['image']); ?>" alt="Service Image" style="max-width: 100px; max-height: 100px;">
                                        <br>
                                        <br>
                                        <label class="btn btn-primary" style="display:block; width:100px; background-color:#337ab7; color:white; border-radius:4px; padding: 6px 12px; text-align:center; cursor:pointer;">Choose File<input type="file" name="home_services_file[<?php echo $index; ?>]" accept="image/*" style="display:none;"></label>
                                        <br>
                                        <button type="button" class="btn btn-warning btn-sm archive-service" data-index="<?php echo $index; ?>">Archive</button>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                            <button type="button" class="btn btn-success btn-sm" id="add_service">Add New Service</button>
                            <button type="submit" class="btn btn-primary btn-sm">Save Services</button>
                            <hr>
                        </form>

                        <form method="POST" action="" enctype="multipart/form-data">
                            <input type="hidden" name="section" value="home_affiliates">
                            <h4>Affiliates</h4>
                            <div id="home_affiliates_container">
                                <?php foreach ($content_data['home']['affiliates'] as $index => $affiliate): ?>
                                    <div class="form-group affiliate-item" id="affiliate-<?php echo $index; ?>">
                                        <label>Affiliate <?php echo $index + 1; ?></label>
                                        <input type="text" class="form-control" name="home_affiliates[<?php echo $index; ?>][title]" value="<?php echo htmlspecialchars($affiliate['title']); ?>" placeholder="Affiliate Title">
                                        <input type="hidden" name="home_affiliates[<?php echo $index; ?>][image]" value="<?php echo htmlspecialchars($affiliate['image']); ?>">
                                        <img src="../assets/images/<?php echo htmlspecialchars($affiliate['image']); ?>" alt="Affiliate Image" style="max-width: 100px; max-height: 100px;">
                                        <br>
                                        <br>
                                        <label class="btn btn-primary" style="display:block; width:150px; background-color:#337ab7; color:white; border-radius:4px; padding: 6px 12px; text-align:center; cursor:pointer;">Choose File<input type="file" name="home_affiliates_file[<?php echo $index; ?>]" accept="image/*" style="display:none;"></label>
                                        <br>
                                        <input type="text" class="form-control" name="home_affiliates[<?php echo $index; ?>][modal_id]" value="<?php echo htmlspecialchars($affiliate['modal_id']); ?>" placeholder="Modal ID">
                                        <br>
                                        <button type="button" class="btn btn-warning btn-sm archive-affiliate" data-index="<?php echo $index; ?>">Archive</button>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                            <button type="button" class="btn btn-success btn-sm" id="add_affiliate">Add New Affiliate</button>
                            <button type="submit" class="btn btn-primary btn-sm">Save Affiliates</button>
                            <hr>
                        </form>

                        <form method="POST" action="" enctype="multipart/form-data">
                            <input type="hidden" name="section" value="home_affiliate_modals">
                            <h4>Affiliate Modals</h4>
                            <div id="home_affiliate_modals_container">
                                <?php foreach ($content_data['home']['affiliate_modals'] as $modal_id => $modal_data): ?>
                                    <div class="form-group affiliate-modal-item" id="affiliate-modal-<?php echo htmlspecialchars($modal_id); ?>">
                                        <label>Modal ID: <?php echo htmlspecialchars($modal_id); ?></label>
                                        <input type="text" class="form-control" name="home_affiliate_modals[<?php echo htmlspecialchars($modal_id); ?>][title]" value="<?php echo htmlspecialchars($modal_data['title']); ?>" placeholder="Modal Title">
                                        <h5>Content for <?php echo htmlspecialchars($modal_data['title']); ?></h5>
                                        <div class="modal-content-items-container">
                                            <?php foreach ($modal_data['content'] as $item_index => $item): ?>
                                                <div class="form-group modal-content-item">
                                                    <input type="text" class="form-control" name="home_affiliate_modals[<?php echo htmlspecialchars($modal_id); ?>][content][<?php echo $item_index; ?>][name]" value="<?php echo htmlspecialchars($item['name']); ?>" placeholder="Name">
                                                    <input type="text" class="form-control" name="home_affiliate_modals[<?php echo htmlspecialchars($modal_id); ?>][content][<?php echo $item_index; ?>][facebook]" value="<?php echo htmlspecialchars($item['facebook']); ?>" placeholder="Facebook URL">
                                                    <input type="text" class="form-control" name="home_affiliate_modals[<?php echo htmlspecialchars($modal_id); ?>][content][<?php echo $item_index; ?>][contacts]" value="<?php echo htmlspecialchars(implode(',', $item['contacts'])); ?>" placeholder="Contacts (comma-separated)">
                                                    <button type="button" class="btn btn-warning btn-sm remove-modal-content-item">Arhive Item</button>
                                                </div>
                                            <?php endforeach; ?>
                                        </div>
                                        <button type="button" class="btn btn-success btn-sm add-modal-content-item" data-modal-id="<?php echo htmlspecialchars($modal_id); ?>">Add Item to Modal</button>
                                        <button type="button" class="btn btn-warning btn-sm archive-affiliate-modal" data-modal-id="<?php echo htmlspecialchars($modal_id); ?>">Archive Modal</button>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                            <button type="button" class="btn btn-success btn-sm" id="add_affiliate_modal">Add New Affiliate Modal</button>
                            <button type="submit" class="btn btn-primary btn-sm">Save Affiliate Modals</button>
                            <hr>
                        </form>
                    </div>

                    <!-- Rooms Tab Content -->
                    <div class="tab-pane fade" id="rooms" role="tabpanel" aria-labelledby="rooms-tab">
                        <br>
                        <h3>Rooms Content Management</h3>
                        <form method="POST" action="" enctype="multipart/form-data">
                            <input type="hidden" name="section" value="rooms">

                            <div id="rooms_list_container">
                                <?php foreach ($content_data['rooms']['list'] as $index => $room): ?>
                                    <div class="panel panel-default room-item" id="room-<?php echo $index; ?>">
                                        <div class="panel-heading">Room <?php echo $index + 1; ?></div>
                                        <div class="panel-body">
                                            <div class="form-group">
                                                <label>Current Image:</label>
                                                <input type="hidden" name="rooms_list[<?php echo $index; ?>][img]" value="<?php echo htmlspecialchars($room['img']); ?>">
                                                <img src="../assets/images/<?php echo htmlspecialchars($room['img']); ?>" alt="Room Image" style="max-width: 100px; max-height: 100px;">
                                                <label class="btn btn-primary" style="display:block; width:150px; background-color:#337ab7; color:white; border-radius:4px; padding: 6px 12px; text-align:center; cursor:pointer;">Choose File<input type="file" name="rooms_list_file[<?php echo $index; ?>]" accept="image/*" style="display:none;"></label>
                                            </div>
                                            <div class="form-group">
                                                <label>Title:</label>
                                                <input type="text" class="form-control" name="rooms_list[<?php echo $index; ?>][title]" value="<?php echo htmlspecialchars($room['title']); ?>">
                                            </div>
                                            <div class="form-group">
                                                <label>Description:</label>
                                                <textarea class="form-control" name="rooms_list[<?php echo $index; ?>][desc]" rows="3"><?php echo htmlspecialchars($room['desc']); ?></textarea>
                                            </div>
                                            <div class="form-group">
                                                <label>Features (comma-separated):</label>
                                                <input type="text" class="form-control" name="rooms_list[<?php echo $index; ?>][features]" value="<?php echo htmlspecialchars(implode(',', $room['features'])); ?>">
                                            </div>
                                            <div class="form-group">
                                                <label>Price:</label>
                                                <input type="text" class="form-control" name="rooms_list[<?php echo $index; ?>][price]" value="<?php echo htmlspecialchars($room['price']); ?>">
                                            </div>
                                            <button type="button" class="btn btn-warning btn-sm archive-room" data-index="<?php echo $index; ?>">Archive Room</button>
                                            <button type="submit" name="save_item" value="<?php echo $index; ?>" class="btn btn-primary btn-sm">Save Changes</button>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                            <button type="button" class="btn btn-success btn-sm" id="add_room">Add New Room</button>
                            <hr>
                        </form>
                    </div>

                    <!-- Venues Tab Content -->
                    <div class="tab-pane fade" id="venues" role="tabpanel" aria-labelledby="venues-tab">
                        <br>
                        <h3>Venues Content Management</h3>
                        <form method="POST" action="" enctype="multipart/form-data">
                            <input type="hidden" name="section" value="venues">
                    
                            <div id="venues_list_container">
                                <?php foreach ($content_data['venues']['list'] as $index => $venue): ?>
                                    <div class="panel panel-default venue-item" id="venue-<?php echo $index; ?>">
                                        <div class="panel-heading">Venue <?php echo $index + 1; ?></div>
                                        <div class="panel-body">
                                            <div class="form-group">
                                                <label>Current Image:</label>
                                                <input type="hidden" name="venues_list[<?php echo $index; ?>][img]" value="<?php echo htmlspecialchars($venue['img']); ?>">
                                                <img src="../assets/images/<?php echo htmlspecialchars($venue['img']); ?>" alt="Venue Image" style="max-width: 100px; max-height: 100px;">
                                                <label class="btn btn-primary" style="display:block; width:150px; background-color:#337ab7; color:white; border-radius:4px; padding: 6px 12px; text-align:center; cursor:pointer;">Choose File<input type="file" name="venues_list_file[<?php echo $index; ?>]" accept="image/*" style="display:none;"></label>
                                            </div>
                                            <div class="form-group">
                                                <label>Title:</label>
                                                <input type="text" class="form-control" name="venues_list[<?php echo $index; ?>][title]" value="<?php echo htmlspecialchars($venue['title']); ?>">
                                            </div>
                                            <div class="form-group">
                                                <label>Description:</label>
                                                <textarea class="form-control" name="venues_list[<?php echo $index; ?>][desc]" rows="5" style="white-space: pre-wrap;"><?php echo htmlspecialchars($venue['desc']); ?></textarea>
                                                <small class="form-text text-muted">Press Enter for new lines. Formatting will be preserved.</small>
                                            </div>
                                            <div class="form-group">
                                                <label>Modal Content:</label>
                                                <textarea class="form-control" name="venues_list[<?php echo $index; ?>][modal_content]" rows="5" style="white-space: pre-wrap;"><?php echo htmlspecialchars($venue['modal_content']); ?></textarea>
                                                <small class="form-text text-muted">Press Enter for new lines. Formatting will be preserved.</small>
                                            </div>
                                            <button type="button" class="btn btn-warning btn-sm archive-venue" data-index="<?php echo $index; ?>">Archive Venue</button>
                                            <button type="submit" name="save_item" value="<?php echo $index; ?>" class="btn btn-primary btn-sm">Save Changes</button>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                            <button type="button" class="btn btn-success btn-sm" id="add_venue">Add New Venue</button>
                            <hr>
                        </form>
                    </div>

                    <!-- Gallery Tab Content -->
                    <div class="tab-pane fade" id="gallery" role="tabpanel" aria-labelledby="gallery-tab">
                        <br>
                        <h3>Gallery Content Management</h3>
                        <form method="POST" action="" enctype="multipart/form-data">
                            <input type="hidden" name="section" value="gallery">

                            <h4>Add New Gallery Image</h4>
                            <div class="form-group">
                                <label for="new_gallery_image">Image File:</label>
                                <label class="btn btn-primary" style="display:block; width:150px; background-color:#337ab7; color:white; border-radius:4px; padding: 6px 12px; text-align:center; cursor:pointer;">Choose File<input type="file" id="new_gallery_image" name="new_gallery_image" accept="image/*" style="display:none;"></label>
                            </div>
                            <div class="form-group">
                                <label for="new_gallery_caption">Caption:</label>
                                <input type="text" class="form-control" id="new_gallery_caption" name="new_gallery_caption" placeholder="Enter image caption">
                            </div>
                            <div class="form-group">
                                <label for="new_gallery_category">Category:</label>
                                <input type="text" class="form-control" id="new_gallery_category" name="new_gallery_category" placeholder="Enter image category (e.g., event, garden)">
                            </div>
                            <button type="submit" class="btn btn-success">Upload New Image</button>
                            <hr>

                            <h4>Existing Gallery Images</h4>
                            <div id="gallery_images_container">
                                <?php if (isset($content_data['gallery']['images']) && is_array($content_data['gallery']['images'])):
                                    foreach ($content_data['gallery']['images'] as $index => $image): ?>                                        
                                        <div class="panel panel-default gallery-image-item" id="gallery-image-<?php echo $index; ?>">
                                            <div class="panel-body">
                                                <div class="form-group">
                                                    <label>Image:</label>
                                                    <input type="hidden" name="gallery_images[<?php echo $index; ?>][filename]" value="<?php echo htmlspecialchars($image['filename'] ?? ''); ?>">
                                                    <img src="../assets/images/admin/gallery/<?php echo htmlspecialchars($image['filename']); ?>" alt="Gallery Image" style="max-width: 100px; max-height: 100px;">
                                            </div>
                                                <div class="form-group">
                                                    <label>Caption:</label>
                                                    <input type="text" class="form-control" name="gallery_images[<?php echo $index; ?>][caption]" value="<?php echo htmlspecialchars($image['caption'] ?? ''); ?>">
                                                </div>
                                                <div class="form-group">
                                                    <label>Category:</label>
                                                    <input type="text" class="form-control" name="gallery_images[<?php echo $index; ?>][category]" value="<?php echo htmlspecialchars($image['category'] ?? ''); ?>">
                                                </div>
                                                <button type="button" class="btn btn-warning btn-sm archive-gallery-image" data-filename="<?php echo htmlspecialchars($image['filename'] ?? ''); ?>">Archive Image</button>
                                                <button type="submit" name="save_item" value="<?php echo $index; ?>" class="btn btn-primary btn-sm">Save Changes</button>
                                            </div>
                                        </div>
                                    <?php endforeach;
                                endif; ?>
                            </div>
                            <hr>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Confirmation Modal -->
    <div class="modal fade" id="confirmationModal" tabindex="-1" role="dialog" aria-labelledby="confirmationModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="confirmationModalLabel">Confirm Action</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    Are you sure you want to archive this item?
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">No</button>
                    <button type="button" class="btn btn-primary" id="confirmActionButton">Yes</button>
                </div>
            </div>
        </div>
    </div>
    
     <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.bundle.min.js"></script>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/js/bootstrap.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>


    <script>
        $(document).ready(function () {
            $("#sidebarCollapse").on('click', function () {
                $("#sidebar").toggleClass('active');
                $("#content").toggleClass('active');
            });
        });

        $(document).ready(function () {
            $("#prices-content").load("prices.php");
            $("#chatbot-content").load("AdminChatbot.php");
        });

        $(function () {
            $('#contentTabs a').on('click', function (e) {
                e.preventDefault();
                $(this).tab('show');
            });

            // Function to update indices of elements
            function updateIndices($container, namePrefix, itemClass) {
                var baseName = namePrefix.replace(/_\d+/g, ''); // Remove trailing index if present
                $container.children(itemClass).each(function(index) {
                    $(this).attr('id', baseName + '-' + index);
                    $(this).find('[name^="' + baseName + '"]').each(function() {
                        var currentName = $(this).attr('name');
                        // Replace the index part of the name attribute
                        var newName = currentName.replace(new RegExp(baseName + '\[\d+\]'), baseName + '[' + index + ']');
                        $(this).attr('name', newName);
                    });
                    // Update label text if it follows a pattern like 'Item X'
                    var label = $(this).find('label').first();
                    if (label.length && label.text().match(/^(New|Item|Slide|Offer|Service|Affiliate|Room|Venue) \d*$/)) {
                        label.text(baseName.replace(/_/g, ' ').replace(/\b\w/g, l => l.toUpperCase()) + ' ' + (index + 1));
                    }
                });
            }

            // Home Slides
            var slideIndex = <?php echo isset($content_data['home']['slides']) ? count($content_data['home']['slides']) : 0; ?>;
            $('#add_slide').on('click', function() {
                var newSlide = `
                    <div class="form-group slide-item" id="slide-${slideIndex}">
                        <label>Slide ${slideIndex + 1}</label>
                        <input type="text" class="form-control" name="home_slides[${slideIndex}][text]" placeholder="Slide Text">
                        <input type="hidden" name="home_slides[${slideIndex}][image]" value="">
                        <input type="file" name="home_slides_file[${slideIndex}]" accept="image/*">
                        <button type="button" class="btn btn-danger btn-sm remove-slide">Discard</button>
                    </div>
                `;
                $('#home_slides_container').append(newSlide);
                slideIndex++;
            });
            $(document).on('click', '.remove-slide', function() {
                $(this).closest('.slide-item').remove();
                updateIndices($('#home_slides_container'), 'home_slides', '.slide-item');
            });

            // Home Offers
            var offerIndex = <?php echo isset($content_data['home']['offers']) ? count($content_data['home']['offers']) : 0; ?>;
            $('#add_offer').on('click', function() {
                var newOffer = `
                    <div class="form-group offer-item" id="offer-${offerIndex}">
                        <label>Offer ${offerIndex + 1}</label>
                        <input type="hidden" name="home_offers[${offerIndex}][image]" value="">
                        <input type="file" name="home_offers_file[${offerIndex}]" accept="image/*">
                        <button type="button" class="btn btn-danger btn-sm remove-offer">Discard</button>
                    </div>
                `;
                $('#home_offers_container').append(newOffer);
                offerIndex++;
            });
            $(document).on('click', '.remove-offer', function() {
                $(this).closest('.offer-item').remove();
                updateIndices($('#home_offers_container'), 'home_offers', '.offer-item');
            });

            // Home Services
            var serviceIndex = <?php echo isset($content_data['home']['services']) ? count($content_data['home']['services']) : 0; ?>;
            $('#add_service').on('click', function() {
                var newService = `
                    <div class="form-group service-item" id="service-${serviceIndex}">
                        <label>Service ${serviceIndex + 1}</label>
                        <input type="text" class="form-control" name="home_services[${serviceIndex}][title]" placeholder="Service Title">
                        <input type="hidden" name="home_services[${serviceIndex}][image]" value="">
                        <input type="file" name="home_services_file[${serviceIndex}]" accept="image/*">
                        <button type="button" class="btn btn-danger btn-sm remove-service">Discard</button>
                    </div>
                `;
                $('#home_services_container').append(newService);
                serviceIndex++;
            });
            $(document).on('click', '.remove-service', function() {
                $(this).closest('.service-item').remove();
                updateIndices($('#home_services_container'), 'home_services', '.service-item');
            });

            // Home Affiliates
            var affiliateIndex = <?php echo isset($content_data['home']['affiliates']) ? count($content_data['home']['affiliates']) : 0; ?>;
            $('#add_affiliate').on('click', function() {
                var newAffiliate = `
                    <div class="form-group affiliate-item" id="affiliate-${affiliateIndex}">
                        <label>Affiliate ${affiliateIndex + 1}</label>
                        <input type="text" class="form-control" name="home_affiliates[${affiliateIndex}][title]" placeholder="Affiliate Title">
                        <input type="hidden" name="home_affiliates[${affiliateIndex}][image]" value="">
                        <input type="file" name="home_affiliates_file[${affiliateIndex}]" accept="image/*">
                        <input type="text" class="form-control" name="home_affiliates[${affiliateIndex}][modal_id]" placeholder="Modal ID">
                        <button type="button" class="btn btn-danger btn-sm remove-affiliate">Discard</button>
                    </div>
                `;
                $('#home_affiliates_container').append(newAffiliate);
                affiliateIndex++;
            });
            $(document).on('click', '.remove-affiliate', function() {
                $(this).closest('.affiliate-item').remove();
                updateIndices($('#home_affiliates_container'), 'home_affiliates', '.affiliate-item');
            });

            // Rooms
            var roomIndex = <?php echo isset($content_data['rooms']['list']) ? count($content_data['rooms']['list']) : 0; ?>;
            $('#add_room').on('click', function() {
                var newRoom = `
                    <div class="panel panel-default room-item" id="room-${roomIndex}">
                        <div class="panel-heading">New Room</div>
                        <div class="panel-body">
                            <div class="form-group">
                                <label>Image:</label>
                                <input type="hidden" name="rooms_list[${roomIndex}][img]" value="">
                                <input type="file" name="rooms_list_file[${roomIndex}]" accept="image/*">
                            </div>
                            <div class="form-group">
                                <label>Title:</label>
                                <input type="text" class="form-control" name="rooms_list[${roomIndex}][title]">
                            </div>
                            <div class="form-group">
                                <label>Description:</label>
                                <textarea class="form-control" name="rooms_list[${roomIndex}][desc]" rows="3"></textarea>
                            </div>
                            <div class="form-group">
                                <label>Features (comma-separated):</label>
                                <input type="text" class="form-control" name="rooms_list[${roomIndex}][features]">
                            </div>
                            <div class="form-group">
                                <label>Price:</label>
                                <input type="text" class="form-control" name="rooms_list[${roomIndex}][price]">
                            </div>
                            <button type="button" class="btn btn-danger btn-sm remove-room">Remove Room</button>
                        </div>
                    </div>
                `;
                $('#rooms_list_container').append(newRoom);
                roomIndex++;
            });
            $(document).on('click', '.remove-room', function() {
                $(this).closest('.room-item').remove();
                updateIndices($('#rooms_list_container'), 'rooms_list', '.room-item');
            });

            // Venues
            var venueIndex = <?php echo isset($content_data['venues']['list']) ? count($content_data['venues']['list']) : 0; ?>;
            $('#add_venue').on('click', function() {
                var newVenue = `
                    <div class="panel panel-default venue-item" id="venue-${venueIndex}">
                        <div class="panel-heading">New Venue</div>
                        <div class="panel-body">
                            <div class="form-group">
                                <label>Image:</label>
                                <input type="hidden" name="venues_list[${venueIndex}][img]" value="">
                                <input type="file" name="venues_list_file[${venueIndex}]" accept="image/*">
                            </div>
                            <div class="form-group">
                                <label>Title:</label>
                                <input type="text" class="form-control" name="venues_list[${venueIndex}][title]">
                            </div>
                            <div class="form-group">
                                <label>Description:</label>
                                <textarea class="form-control" name="venues_list[${venueIndex}][desc]" rows="3"></textarea>
                            </div>
                            <div class="form-group">
                                <label>Modal Content:</label>
                                <textarea class="form-control" name="venues_list[${venueIndex}][modal_content]" rows="3"></textarea>
                            </div>
                            <button type="button" class="btn btn-danger btn-sm remove-venue">Remove Venue</button>
                        </div>
                    </div>
                `;
                $('#venues_list_container').append(newVenue);
                venueIndex++;
            });
            $(document).on('click', '.remove-venue', function() {
                $(this).closest('.venue-item').remove();
                updateIndices($('#venues_list_container'), 'venues_list', '.venue-item');
            });

            // Affiliate Modals - Add Item
            $(document).on('click', '.add-modal-content-item', function() {
                var modalId = $(this).data('modal-id');
                var contentContainer = $(this).siblings('.modal-content-items-container');
                var itemCount = contentContainer.find('.modal-content-item').length;
                var newItem = `
                    <div class="form-group modal-content-item">
                        <input type="text" class="form-control" name="home_affiliate_modals[${modalId}][content][${itemCount}][name]" placeholder="Name">
                        <input type="text" class="form-control" name="home_affiliate_modals[${modalId}][content][${itemCount}][facebook]" placeholder="Facebook URL">
                        <input type="text" class="form-control" name="home_affiliate_modals[${modalId}][content][${itemIndex}][contacts]" placeholder="Contacts (comma-separated)">
                        <button type="button" class="btn btn-danger btn-sm remove-modal-content-item">Discard</button>
                    </div>
                `;
                contentContainer.append(newItem);
            });

            // Affiliate Modals - Remove Item
            $(document).on('click', '.remove-modal-content-item', function() {
                $(this).closest('.modal-content-item').remove();
                // Re-index items within the modal
                var modalId = $(this).closest('.affiliate-modal-item').attr('id').replace('affiliate-modal-', '');
                $(this).closest('.modal-content-items-container').find('.modal-content-item').each(function(index) {
                    $(this).find('[name*="[content]"]').each(function() {
                        var currentName = $(this).attr('name');
                        var newName = currentName.replace(/\w+\[content\]\[\d+\]/, 'content[' + index + ']');
                        $(this).attr('name', newName);
                    });
                });
            });

            // Add New Affiliate Modal
            var affiliateModalIndex = 0;
            $('#add_affiliate_modal').on('click', function() {
                var newModalId = 'new_modal_' + affiliateModalIndex;
                var newModal = `
                    <div class="form-group affiliate-modal-item" id="affiliate-modal-${newModalId}">
                        <label>New Modal ID: ${newModalId}</label>
                        <input type="text" class="form-control" name="home_affiliate_modals[${newModalId}][title]" placeholder="Modal Title">
                        <h5>Content for New Modal</h5>
                        <div class="modal-content-items-container">
                            <!-- Initial empty state or add button -->
                        </div>
                        <button type="button" class="btn btn-success btn-sm add-modal-content-item" data-modal-id="${newModalId}">Add Item to Modal</button>
                        <button type="button" class="btn btn-danger btn-sm remove-affiliate-modal">Remove Modal</button>
                    </div>
                `;
                $('#home_affiliate_modals_container').append(newModal);
                affiliateModalIndex++;
            });

            // Remove Affiliate Modal
            $(document).on('click', '.remove-affiliate-modal', function() {
                $(this).closest('.affiliate-modal-item').remove();
            });

            // Archive confirmation logic
            var archiveData = {};
            $(document).on('click', '[class*="archive-"]', function() {
                var button = $(this);
                archiveData = {}; // Reset

                if (button.hasClass('archive-gallery-image')) {
                    archiveData.section = 'archive_gallery_image';
                    archiveData.filename = button.data('filename');
                } else if (button.hasClass('archive-venue')) {
                    archiveData.section = 'archive_venue';
                    archiveData.index = button.data('index');
                } else if (button.hasClass('archive-room')) {
                    archiveData.section = 'archive_room';
                    archiveData.index = button.data('index');
                } else if (button.hasClass('archive-affiliate-modal')) {
                    archiveData.section = 'archive_affiliate_modal';
                    archiveData.modal_id = button.data('modal-id');
                } else if (button.hasClass('archive-affiliate')) {
                    archiveData.section = 'archive_affiliate';
                    archiveData.index = button.data('index');
                } else if (button.hasClass('archive-service')) {
                    archiveData.section = 'archive_service';
                    archiveData.index = button.data('index');
                } else if (button.hasClass('archive-offer')) {
                    archiveData.section = 'archive_offer';
                    archiveData.index = button.data('index');
                } else if (button.hasClass('archive-slide')) {
                    archiveData.section = 'archive_slide';
                    archiveData.index = button.data('index');
                }

                if(archiveData.section) {
                    $('#confirmationModal').modal('show');
                }
            });

            $('#confirmActionButton').on('click', function() {
                if (archiveData.section) {
                    $.ajax({
                        url: 'manage_content.php',
                        type: 'POST',
                        data: archiveData,
                        success: function(response) {
                            $('#confirmationModal').modal('hide');
                            location.reload();
                        },
                        error: function() {
                            $('#confirmationModal').modal('hide');
                            toastr.error('An error occurred. Please try again.');
                        }
                    });
                }
            });
        });


        document.addEventListener("DOMContentLoaded", function () {
        const alerts = document.querySelectorAll(".warning");
        alerts.forEach(alert => {
            setTimeout(() => {
            alert.classList.add("fade");
            setTimeout(() => {
                if (alert && alert.parentNode) {
                alert.parentNode.removeChild(alert);
                }
            },); // allow fade-out
            }, 1500); // 3 seconds delay
        });
        });
    </script>
</body>
</html>