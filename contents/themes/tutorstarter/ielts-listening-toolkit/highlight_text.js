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
                <i id="highlight-icon-modify" class="fa-solid fa-trash"></i>
                <i  id="highlight-icon-modify" onclick="yellow_highlight('${uniqueId}')"class="fa-solid fa-square" style="color: #FFD43B;"></i>
                <i id="highlight-icon-modify" onclick="green_highlight('${uniqueId}')" class="fa-solid fa-square" style="color: #49e411;"></i>
                <i  id="highlight-icon-modify" onclick="blue_highlight('${uniqueId}')" class="fa-solid fa-square" style="color: #74C0FC;"></i>
                <i id="highlight-icon-modify" onclick="purple_highlight('${uniqueId}')" class="fa-solid fa-square" style="color: #B197FC;"></i>
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
