<?php
/*
 * Template Name: Document Template
 * Template Post Type: document
 * Description: Template for displaying a document.
 
 */

if (!defined("ABSPATH")) {
    exit(); // Exit if accessed directly.
}
get_header();
    $post_id = get_the_ID();
    $user_id = get_current_user_id();
    $current_user = wp_get_current_user();
    $current_username = $current_user->user_login;
    $username = $current_username;
    $current_user_id = $current_user->ID;
    echo '
    <script>   
        var CurrentuserID = "' . $user_id . '";
        var Currentusername = "' . $username . '";
    </script>
    ';

    //$document_id = get_post_meta($post_id, "_digitalsat_custom_number", true);
    $document_id = get_query_var('document_id');
    // Database credentials
    $servername = DB_HOST;
    $username = DB_USER;
    $password = DB_PASSWORD;
    $dbname = DB_NAME;

    // Create connection
    $conn = new mysqli($servername, $username, $password, $dbname);
    $conn->set_charset("utf8mb4");

    // Check connection
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    $site_url = get_site_url();

        
        
    echo "<script> 
        var siteUrl = '" .$site_url . "';
        var site_url = '" .$site_url . "';
        var document_id = '" . $document_id . "';
    </script>";
    
    echo '<style>
        .document-container {
            display: flex;
            gap: 30px;
            max-width: 1200px;
            margin: 0 auto;
            padding: 20px;
        }
        
        .main-content {
            flex: 0 0 70%;
            background: #fff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        
        .sidebar {
            flex: 0 0 30%;
            background: #fff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            height: fit-content;
        }
        
        .document-title {
            font-size: 2em;
            color: #333;
            margin-bottom: 15px;
        }
        
        .document-meta {
            display: flex;
            flex-wrap: wrap;
            gap: 15px;
            margin-bottom: 20px;
            font-size: 0.9em;
            color: #666;
        }
        
        .document-meta span {
            background: #f5f5f5;
            padding: 5px 10px;
            border-radius: 4px;
        }
        
        .document-description {
            margin-bottom: 20px;
            line-height: 1.6;
            color: #444;
        }
        
        .document-viewer {
            margin: 20px 0;
            border: 1px solid #ddd;
            border-radius: 4px;
            overflow: hidden;
        }
        
        .download-btn {
            display: inline-block;
            background: #007bff;
            color: #fff;
            padding: 10px 20px;
            border-radius: 4px;
            text-decoration: none;
            transition: background 0.3s;
        }
        
        .download-btn:hover {
            background: #0056b3;
        }
        
        .related-documents {
            list-style: none;
            padding: 0;
            margin: 0;
        }
        
        .related-documents li {
            border-bottom: 1px solid #eee;
            padding: 10px 0;
        }
        
        .related-documents a {
            color: #333;
            text-decoration: none;
            display: block;
            margin-bottom: 5px;
        }
        
        .related-documents a:hover {
            color: #007bff;
        }
        
        .related-date {
            font-size: 0.85em;
            color: #888;
        }
        
        @media (max-width: 768px) {
            .document-container {
                flex-direction: column;
            }
            
            .main-content,
            .sidebar {
                flex: 0 0 100%;
            }
        }
    </style>';

    // Query to fetch document details
    $sql = "SELECT document_name, document_id, category, tag, content, file_link, file_type, prices, created_at, updated_at
        FROM document 
        WHERE document_id = ?";

    // Query to fetch related documents
    $sql_related = "SELECT document_name, document_id, updated_at 
                    FROM document 
                    WHERE category = ? AND document_id != ? 
                    ORDER BY updated_at DESC 
                    LIMIT 10";

    // Prepare and execute the main query
    $stmt = $conn->prepare($sql);
    if (!$stmt) {
        die("Error preparing statement 1: " . $conn->error);
    }
    $stmt->bind_param("s", $document_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        
        // Decode the JSON tag field
        $tags = json_decode($row['tag'], true);
        $tag_list = implode(', ', $tags);
        
        echo '<div class="document-container">';
        
        // Main content area (70%)
        echo '<div class="main-content">';
        echo '<h1 class="document-title">' . htmlspecialchars($row['document_name']) . '</h1>';
        echo '<div class="document-meta">';
        echo '<span class="category">Category: ' . htmlspecialchars($row['category']) . '</span>';
        echo '<span class="tags">Tags: ' . htmlspecialchars($tag_list) . '</span>';
        echo '<span class="updated-at">Last updated: ' . date('F j, Y', strtotime($row['updated_at'])) . '</span>';
        echo '</div>';
        
        echo '<div class="document-description">';
        echo htmlspecialchars($row['content']);
        echo '</div>';
        
        if ($row['file_type'] === 'pdf') {
            echo '<div class="document-viewer">';
            echo '<iframe src="' . htmlspecialchars($row['file_link']) . '" width="100%" height="600px"></iframe>';
            echo '</div>';
        }
        
        echo '<div class="document-actions">';
        echo '<a href="' . htmlspecialchars($row['file_link']) . '" class="download-btn" download>Download Document</a>';
        echo '</div>';
        echo '</div>';
        
        // Sidebar (30%)
        echo '<div class="sidebar">';
        echo '<h3>Related Documents</h3>';
        
        // Prepare and execute related documents query
        $stmt_related = $conn->prepare($sql_related);
        $stmt_related->bind_param("ss", $row['category'], $document_id);
        $stmt_related->execute();
        $result_related = $stmt_related->get_result();
        
        echo '<ul class="related-documents">';
        while ($related = $result_related->fetch_assoc()) {
            echo '<li>';
            echo '<a href="' . $site_url . '/document/' . $related['document_id'] . '">';
            echo htmlspecialchars($related['document_name']);
            echo '</a>';
            echo '<span class="related-date">' . date('M j, Y', strtotime($related['updated_at'])) . '</span>';
            echo '</li>';
        }
        echo '</ul>';
        echo '</div>';
        
        echo '</div>'; // Close document-container
        
    } else {
        get_header();
        echo "<p>Không tìm thấy tài liệu.</p>";
        exit();
    }

get_footer();

