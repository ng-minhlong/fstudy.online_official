document.addEventListener('DOMContentLoaded', function() {
    // Listen for mouseup event on the document
    document.addEventListener('mouseup', function() {
        // Get the selected text
        var selection = window.getSelection();
        var selectedText = selection.toString();

        // Check if any text is selected
        if (selectedText !== '') {
            // Highlight the selected text
            highlightSelectedText(selection, currentPartIndex);
        }

        
    });

    
    function deleteHighlight(span, tooltipText) {
        // Create a document fragment to hold the text nodes
        const fragment = document.createDocumentFragment();
    
        // Move all child nodes except the tooltip to the fragment
        while (span.firstChild && span.firstChild !== tooltipText) {
            fragment.appendChild(span.firstChild);
        }
    
        // Replace the span with the content in the fragment
        span.parentNode.replaceChild(fragment, span);
    }

    
    function highlightSelectedText(selection, currentPartIndex) {
        if (selection.rangeCount > 0) {
            // Get the range of the selected text
            var range = selection.getRangeAt(0);

            // Create a new span element to wrap the selected text
            var span = document.createElement('span');
            span.className = 'tooltip';
            span.style.backgroundColor = 'yellow';  // Default background color

            // Create a unique ID for the span
            var uniqueId = 'myTooltip_' + new Date().getTime();
            span.id = uniqueId;

            // Surround the selected text with the span element
            range.surroundContents(span);

            highlights[uniqueId] = {
                text: selection.toString(),
                color: 'yellow',
                currentPartIndex: `${currentPartIndex}`,
            };


            // Create the tooltip content with buttons
            var tooltipText = document.createElement('span');
            tooltipText.className = 'tooltiptext';
            tooltipText.innerHTML = `
                <i id="highlight-icon-modify" onclick="deleteHighlight(span, tooltipText)" class="fa-solid fa-trash"></i>
                <i  id="highlight-icon-modify" onclick="yellow_highlight('${uniqueId}')"class="fa-solid fa-square" style="color: #FFD43B;"></i>
                <i id="highlight-icon-modify" onclick="green_highlight('${uniqueId}')" class="fa-solid fa-square" style="color: #49e411;"></i>
                <i  id="highlight-icon-modify" onclick="blue_highlight('${uniqueId}')" class="fa-solid fa-square" style="color: #74C0FC;"></i>
                <i id="highlight-icon-modify" onclick="purple_highlight('${uniqueId}')" class="fa-solid fa-square" style="color: #B197FC;"></i>
                <i id="highlight-icon-modify" onclick="addToNotation('${uniqueId}')" class="fa-solid fa-plus"></i>

                `;

            // Add the tooltip content to the span
            span.appendChild(tooltipText);

            // Add click event listener to the span element
            span.addEventListener('click', function(event) {
                // Toggle the tooltip visibility
                tooltipText.style.visibility = (tooltipText.style.visibility === 'visible') ? 'hidden' : 'visible';
                tooltipText.style.opacity = (tooltipText.style.opacity === '1') ? '0' : '1';

                // Prevent the event from bubbling up to document level
                event.stopPropagation();
            });

            // Add click event listener to the delete button
            tooltipText.querySelector('.fa-trash').addEventListener('click', function (event) {
                deleteHighlight(span, tooltipText);
                // Prevent the event from bubbling up to document level
                event.stopPropagation();
            });

            // Close the tooltip when clicking outside of it
            document.addEventListener('click', function(event) {
                if (!span.contains(event.target)) {
                    tooltipText.style.visibility = 'hidden';
                    tooltipText.style.opacity = '0';
                }
            });
        }
    }
});



function addToNotation(spanId) {
    let notationWord = document.getElementById(`${spanId}`).innerText;

    (async () => {
        const { value: notationSaveWord } = await Swal.fire({
            title: "Notation",
            html: `Lưu từ <strong>${notationWord}</strong> vào mục Notation?`,
            input: "select",
            inputOptions: {
                quick_save: "Lưu nhanh",
                detail_save: "Thêm định nghĩa"
            },
            inputPlaceholder: "Lựa chọn lưu",
            showCancelButton: true,
            inputValidator: (value) => {
                return !value ? "Vui lòng chọn phương án!" : undefined;
            }
        });

        if (notationSaveWord === "quick_save") {
            await saveNotation(notationWord, "", "web", pre_id_test_ || "0", "Ielts Reading");
            Swal.fire("Lưu thành công!", "Từ đã được lưu nhanh.", "success");
        } else if (notationSaveWord === "detail_save") {
            const { value: definition } = await Swal.fire({
                title: "Nhập định nghĩa",
                input: "text",
                inputPlaceholder: "Nhập định nghĩa cho từ...",
                showCancelButton: true,
                inputValidator: (value) => {
                    return !value ? "Vui lòng nhập định nghĩa!" : undefined;
                }
            });

            if (definition) {
                await saveNotation(notationWord, definition, "web", pre_id_test_ || "0", "Ielts Reading");
                Swal.fire("Lưu thành công!", "Từ và định nghĩa đã được lưu.", "success");
            }
        }
        resultId++;
        async function saveNotation(word, definition, source, id_test, test_type) {
            const saveTime = new Date().toISOString().split('T')[0]; // Chỉ lấy phần ngày
            await fetch(`${siteUrl}/wp-json/api/v1/save-notation`, {
                method: "POST",
                headers: {
                    "Content-Type": "application/x-www-form-urlencoded"
                },
                body: new URLSearchParams({
                    action: "save_notation",
                    word_save: word,
                    meaning_or_explanation: definition,
                    save_time: saveTime,
                    is_source: source,
                    username: currentUsername,
                    user_id: currentUserid,
                    id_test: id_test,
                    id_note: resultId,
                    test_type: test_type,
                })
            });
        }
    })();
}


