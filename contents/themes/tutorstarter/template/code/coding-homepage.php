<?php


/*
 * Template Name: Coding Template
 * Template Post Type: coding area
 
 */


if (!defined("ABSPATH")) {
    exit(); // Exit if accessed directly.
}

   

    add_filter('document_title_parts', function ($title) {
        $title['title'] = "Code Homepage";
        return $title;
    });
        
    get_header();
    $site_url = get_site_url();
    echo "<script> 
        var siteUrl = '" .$site_url . "';      
    </script>";
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Interactive Button Group</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

      
        .title {
            font-size: 2.8rem;
            color: #3a4a6d;
            margin-bottom: 3rem;
            text-align: center;
            text-shadow: 1px 1px 3px rgba(0, 0, 0, 0.1);
            font-weight: 600;
        }

        .button-group {
            display: flex;
            gap: 70px;
            flex-wrap: wrap;
            justify-content: center;
        }

        .btn {
            position: relative;
            width: 300px;
            height: 300px;
            border: none;
            border-radius: 25px;
            background: white;
            color: #3a4a6d;
            font-size: 1.3rem;
            font-weight: 600;
            cursor: pointer;
            box-shadow: 0 15px 30px rgba(0, 0, 0, 0.1);
            transition: all 0.3s ease;
            overflow: hidden;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }

        .btn::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: linear-gradient(135deg, rgba(255,255,255,0.3) 0%, rgba(255,255,255,0) 100%);
            border-radius: 25px;
            z-index: 1;
        }

        .btn i {
            font-size: 3.5rem;
            margin-bottom: 15px;
            transition: all 0.3s ease;
        }

        .btn:hover {
            transform: translateY(-8px);
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.2);
        }

        .btn:active {
            transform: translateY(0);
            box-shadow: 0 10px 20px rgba(0, 0, 0, 0.15);
        }

        .btn-1 {
            background: linear-gradient(45deg, #ff9a9e 0%, #fad0c4 100%);
        }

        .btn-2 {
            background: linear-gradient(45deg, #a1c4fd 0%, #c2e9fb 100%);
        }

        .btn-3 {
            background: linear-gradient(45deg, #ffecd2 0%, #fcb69f 100%);
        }
        .btn-4 {
            background: linear-gradient(45deg,rgb(221, 140, 25) 0%, #fcb69f 100%);
        }

        .btn:hover i {
            transform: scale(1.15);
        }

        .ripple {
            position: absolute;
            background: rgba(255, 255, 255, 0.4);
            border-radius: 50%;
            transform: scale(0);
            animation: ripple 0.6s linear;
            pointer-events: none;
        }
        .code-content{
            height: auto;
        }

        @keyframes ripple {
            to {
                transform: scale(3);
                opacity: 0;
            }
        }

        @media (max-width: 768px) {
            .button-group {
                gap: 20px;
            }
            
            .btn {
                width: 160px;
                height: 160px;
                font-size: 1.2rem;
            }
            
            .btn i {
                font-size: 3rem;
            }
        }

        @media (max-width: 500px) {
            .button-group {
                flex-direction: column;
                align-items: center;
                gap: 15px;
            }
            
            .btn {
                width: 240px;
                height: 100px;
                flex-direction: row;
                justify-content: flex-start;
                padding-left: 30px;
                border-radius: 15px;
            }
            
            .btn i {
                margin-bottom: 0;
                margin-right: 20px;
                font-size: 2.5rem;
            }
            
            .title {
                font-size: 2.2rem;
                margin-bottom: 2rem;
            }
        }
    </style>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
</head>
<body>
    <div class = "code-content">
        <h1 class="title">Luyện Code</h1>
        <div class="button-group">
            <button class="btn btn-1" id="likeBtn">
                <i class="fas fa-code"></i>
                Code Ground
            </button>
            <button class="btn btn-2" id="practiceBtn">
                <i class="fas fa-laptop-code"></i>
                Practice
            </button>
            <button class="btn btn-3" id="contestBtn">
                <i class="fas fa-laptop-code"></i>
                Contest
            </button>
            <button class="btn btn-4" id="downloadBtn">
                <i class="fas fa-laptop-file"></i>
                Learn
            </button>
        </div>
    </div>

    <script>
        hidePreloader();
        // Ripple effect function
        function createRipple(event) {
            const button = event.currentTarget;
            
            const ripple = document.createElement("span");
            ripple.classList.add("ripple");
            button.appendChild(ripple);
            
            const rect = button.getBoundingClientRect();
            const size = Math.max(rect.width, rect.height);
            const x = event.clientX - rect.left - size / 2;
            const y = event.clientY - rect.top - size / 2;
            
            ripple.style.width = ripple.style.height = `${size}px`;
            ripple.style.left = `${x}px`;
            ripple.style.top = `${y}px`;
            
            setTimeout(() => {
                ripple.remove();
            }, 600);
        }

        // Button redirect functionality
        document.getElementById('likeBtn').addEventListener('click', function(e) {
            createRipple(e);
            setTimeout(() => {
                // Replace with your actual URL
                window.location.href = `${siteUrl}/code/playground`;
            }, 300); // Small delay to allow ripple animation to show
        });
        document.getElementById('practiceBtn').addEventListener('click', function(e) {
            createRipple(e);
            setTimeout(() => {
                // Replace with your actual URL
                window.location.href = `${siteUrl}/code/practice`;
            }, 300);
        });

        document.getElementById('contestBtn').addEventListener('click', function(e) {
            createRipple(e);
            setTimeout(() => {
                // Replace with your actual URL
                window.location.href = `${siteUrl}/code/contest`;
            }, 300);
        });

        document.getElementById('downloadBtn').addEventListener('click', function(e) {
            createRipple(e);
            setTimeout(() => {
                // Replace with your actual URL
                window.location.href = 'https://example.com/download';
            }, 300);
        });

        // Optional: Add keyboard accessibility
        document.querySelectorAll('.btn').forEach(button => {
            button.addEventListener('keypress', function(e) {
                if (e.key === 'Enter' || e.key === ' ') {
                    e.preventDefault();
                    this.click();
                }
            });
        });
    </script>
</body>
</html>

<?php 
    get_footer();