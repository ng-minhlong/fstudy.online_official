<?php
/*
 * Template Name: Playground Coding
 * Template Post Type: code-playground
 
 */

    if (!defined("ABSPATH")) {
        exit(); // Exit if accessed directly.
    }
    add_filter('document_title_parts', function ($title) {
            $title['title'] = "Code Playground";
            return $title;
        });
            
    get_header();
    $site_url = get_site_url();
    echo "<script> 
        var siteUrl = '" .$site_url . "';      
    </script>";
echo" <link rel='stylesheet' href='" . $site_url . "/contents/themes/tutorstarter/template/code/assets/style.css'>";

?>

<!DOCTYPE html>
<html lang="en">
<style>
    body {
        background-color: #2d2f3d;
        font-family: Arial, sans-serif;
        color: #fff;
    }
</style>
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Play Ground</title>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.2/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-OERcA2EqjJCMA+/3y+gxIOqMEjwtxJY7qPCqsdltbNJuaOe923+mo//f6V8Qbsw3"
        crossorigin="anonymous"></script>
    <link rel="stylesheet" href="http://localhost/fstudy/nodejs-api/code-complier/codemirror-5.65.17/lib/codemirror.css">
    <script src="http://localhost/fstudy/nodejs-api/code-complier/codemirror-5.65.17/mode/python/python.js"></script>

    <script src="http://localhost/fstudy/nodejs-api/code-complier/codemirror-5.65.17/lib/codemirror.js"></script>
    <script src="http://localhost/fstudy/nodejs-api/code-complier/codemirror-5.65.17/mode/clike/clike.js"></script>
    <link rel="stylesheet" href="http://localhost/fstudy/nodejs-api/code-complier/codemirror-5.65.17/theme/dracula.css">
    <script src="http://localhost/fstudy/nodejs-api/code-complier/codemirror-5.65.17/addon/edit/closebrackets.js"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.9.1/font/bootstrap-icons.css">
</head>


<body>
    <div class="row-code m-3">
        <div class="cols-code">
            <div class="d-flex justify-content-between mb-2 bg-dark rounded p-2">
                <div class="col-12 w-25">
                    <label class="visually-hidden" for="inlineFormSelectPref">Chọn ngôn ngữ</label>
                    <select  class="dropdown" id="inlineFormSelectPref">
                        <option selected>Choose...</option>
                        <option value="Java">Java</option>
                        <option value="Cpp">Cpp</option>
                        <option value="Python">Python</option>
                    </select>
                </div>
                <div>
                    <button type="button" class="btn btn-success">Coding Sprint</button>
                    <button type="button" id="run" class="btn btn-success"><i class="bi bi-play-fill"></i></button>
                </div>
            </div>
            <textarea class = "textarea-code" type="text" id="editor" class="form-control" aria-label="First name"></textarea>
        </div>
        <div class="cols-code d-flex flex-column rounded bg-dark px-4">
            <div class="h-50">
                <label for="Input" class="text-light mt-4 mb-2">Input</label>
                <textarea class = "textarea-code" type="text" id="input" class="form-control h-75" aria-label="Last name"></textarea>
            </div>
            <div class="h-50">
                <label for="Output" class="text-light mb-2">Output</label>
                <textarea class = "textarea-code" type="text" id="output" class="form-control h-75" aria-label="Last name"></textarea>
            </div>
        </div>
    </div>
</body>
<script>
    var editor = CodeMirror.fromTextArea(document.getElementById("editor"), {
        mode: "text/x-c++src",
        theme: "dracula",
        lineNumbers: true,
        autoCloseBrackets: true,

    })
    var width = window.innerWidth
    var input = document.getElementById("input")
    var output = document.getElementById("output")
    var run = document.getElementById("run")
    editor.setSize(0.7 * width, "500")
    var option = document.getElementById("inlineFormSelectPref")
    option.addEventListener("change", function () {
        if (option.value == "Java") {
            editor.setOption("mode", "text/x-java")
        }
        else if (option.value == "python") {
            editor.setOption("mode", "text/x-python")
        }
        else {
            editor.setOption("mode", "text/x-c++src")
        }
    })
    var code;
    run.addEventListener("click", async function () {
        code = {
            code: editor.getValue(),
            input: input.value,
            lang: option.value
        }
        console.log(code)
        var oData = await fetch("http://localhost:8000/api/compile", {
            method: "POST",
            headers: {
                "Content-Type": "application/json"
            },
            body: JSON.stringify(code)
        })
        var d = await oData.json()
        output.value = d.output;
        var delayInMilliseconds = 6000; //1 second

    setTimeout(function() {
        var deletefile =  fetch("http://localhost:8000/", {})
        console.log("Delete successfully !")
    }, delayInMilliseconds);
        
    })

    


</script>

</html>