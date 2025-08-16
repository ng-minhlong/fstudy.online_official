// Dev hover + add-category + change-type (JS only)
// Usage: call devOnMode(partIndex, idTest) once page đã load
(function(){
    // List of allowed category options (the values you gave)
    const CATEGORY_OPTIONS = [
      { value: "multiple_choice", label: "Multiple Choice Questions (MCQs)" },
      { value: "matching_heading", label: "Matching Heading" },
      { value: "short-answer_question", label: "Short-Answer Questions" },
      { value: "completion", label: "Completion" },
      { value: "true/false/notgiven_or_yes/no/notgiven", label: "True/False/Not Given or Yes/No/Not Given" },
      { value: "matching_information/features", label: "Matching Information/Features" }
    ];
  
    // helper to create elements
    function el(tag, props = {}, children = []) {
      const e = document.createElement(tag);
      for (const k in props) {
        if (k === 'class') e.className = props[k];
        else if (k === 'style') Object.assign(e.style, props[k]);
        else e.setAttribute(k, props[k]);
      }
      (Array.isArray(children) ? children : [children]).forEach(c => {
        if (typeof c === 'string') e.appendChild(document.createTextNode(c));
        else if (c) e.appendChild(c);
      });
      return e;
    }
  
    // Parse range inside a container's text: look for "Question 1-6", "Q 1-6", or any "1-6"
    function parseRangeFromText(text) {
      if (!text) return null;
      // try explicit "Question" or "Q"
      const re1 = /\b(?:Question|Q)\.?\s*(\d+)\s*(?:-|–|—|to)\s*(\d+)\b/i;
      let m = text.match(re1);
      if (m) return { start: parseInt(m[1],10), end: parseInt(m[2],10) };
      // fallback: any standalone number-range like "1-6"
      const re2 = /\b(\d{1,4})\s*(?:-|–|—)\s*(\d{1,4})\b/;
      m = text.match(re2);
      if (m) return { start: parseInt(m[1],10), end: parseInt(m[2],10) };
      // fallback: "from 1 to 6"
      const re3 = /\bfrom\s+(\d+)\s+to\s+(\d+)\b/i;
      m = text.match(re3);
      if (m) return { start: parseInt(m[1],10), end: parseInt(m[2],10) };
      return null;
    }
  
    // ---------- ADD CATEGORY MODAL (unchanged behavior) ----------
    function showAddCategoryModal(container, partIndex, idTest) {
        // Gather context text for this container
        const ctxText = (container.innerText || '') + '\n' +
                        (container.previousElementSibling ? container.previousElementSibling.innerText : '') + '\n' +
                        (container.nextElementSibling ? container.nextElementSibling.innerText : '');
        const parsed = parseRangeFromText(ctxText); // may be null
      
        // --- compute global minimum start across all group-containers ---
        let minStart = Infinity;
        document.querySelectorAll('.group-container').forEach(c => {
          const t = (c.innerText || '') + '\n' +
                    (c.previousElementSibling ? c.previousElementSibling.innerText : '') + '\n' +
                    (c.nextElementSibling ? c.nextElementSibling.innerText : '');
          const p = parseRangeFromText(t);
          if (p && Number.isInteger(p.start)) {
            if (p.start < minStart) minStart = p.start;
          }
        });
        if (!isFinite(minStart)) minStart = 1; // if nothing found, assume 1
        const offset = (minStart > 1) ? (minStart - 1) : 0; // amount to subtract to normalize to start-from-1
      
        // modal overlay
        const overlay = el('div', { class: 'dev-add-cat-overlay', style: {
          position: 'fixed', left: '0', top: '0', right: '0', bottom: '0',
          display: 'flex', alignItems: 'center', justifyContent: 'center',
          zIndex: '99999', background: 'rgba(0,0,0,0.3)'
        }});
      
        const box = el('div', { class: 'dev-add-cat-box', style: {
          minWidth: '320px', maxWidth: '720px', padding: '16px', borderRadius: '8px',
          background: '#fff', boxShadow: '0 6px 24px rgba(0,0,0,0.2)'
        }});
      
        const title = el('h3', {}, [`Add category (Part ${partIndex}, Test ${idTest})`]);
        const info = el('div', { style: { marginBottom: '8px', fontSize: '13px', color: '#333' } }, [
          // show helpful hint about normalization
          `Hệ thống cố gắng lấy range từ nội dung. Nếu các Q bắt đầu từ ${minStart} trên trang, hệ thống sẽ chuẩn hoá để bắt đầu từ 1 (trừ ${offset}). Bạn vẫn có thể chỉnh tay start/end.`
        ]);
      
        const select = el('select', { style: { width: '100%', padding: '8px', marginBottom: '8px' } });
        CATEGORY_OPTIONS.forEach(opt => {
          select.appendChild(el('option', { value: opt.value }, opt.label));
        });
      
        const row = el('div', { style: { display: 'flex', gap: '8px', marginBottom: '8px' } });
        const startInput = el('input', { type: 'number', placeholder: 'start', style: { flex: '1', padding: '8px' } });
        const endInput = el('input', { type: 'number', placeholder: 'end', style: { flex: '1', padding: '8px' } });
      
        // If we parsed a range, normalize it by subtracting offset so it becomes relative-to-1
        if (parsed) {
          const displayStart = Math.max(1, parsed.start - offset);
          const displayEnd = Math.max(displayStart, parsed.end - offset);
          startInput.value = displayStart;
          endInput.value = displayEnd;
        }
      
        row.appendChild(startInput);
        row.appendChild(endInput);
      
        const btnRow = el('div', { style: { display: 'flex', justifyContent: 'flex-end', gap: '8px' }});
        const cancelBtn = el('button', { type: 'button', style: { padding: '8px 12px' } }, 'Cancel');
        const saveBtn = el('button', { type: 'button', style: { padding: '8px 12px' } }, 'Save');
      
        const status = el('div', { style: { marginTop: '8px', fontSize: '13px' } }, '');
      
        btnRow.appendChild(cancelBtn);
        btnRow.appendChild(saveBtn);
      
        box.appendChild(title);
        box.appendChild(info);
        box.appendChild(select);
        box.appendChild(row);
        box.appendChild(btnRow);
        box.appendChild(status);
        overlay.appendChild(box);
        document.body.appendChild(overlay);
      
        function close() { overlay.remove(); }
        cancelBtn.addEventListener('click', close);
      
        saveBtn.addEventListener('click', async function() {
          const startVal = parseInt(startInput.value, 10);
          const endVal = parseInt(endInput.value, 10);
          const typeVal = select.value;
          if (!Number.isInteger(startVal) || !Number.isInteger(endVal) || startVal <= 0 || endVal <= 0 || endVal < startVal) {
            status.textContent = 'Vui lòng nhập start/end hợp lệ (end >= start).';
            status.style.color = 'red';
            return;
          }
      
          // startVal/endVal are already normalized (relative to 1). Send them as-is.
          const categoryStructure = [{ start: String(startVal), end: String(endVal), type: typeVal }];
      
          status.textContent = 'Đang gửi...';
          status.style.color = '#333';
      
          try {
            // NOTE: keep your existing endpoint pattern; here I use the same as your snippet
            const resp = await fetch(`${siteUrl}/api/v1/dev/ielts/reading_listening/update-category`, {
              method: 'POST',
              headers: { 'Content-Type': 'application/json' },
              body: JSON.stringify({
                idTest: idTest,
                partIndex: partIndex,
                typeRequest: 'ielts_reading',
                category: categoryStructure
              })
            });
      
            if (!resp.ok) {
              const txt = await resp.text();
              throw new Error('Server trả lỗi: ' + resp.status + ' ' + txt);
            }
            const json = await resp.json();
            if (json.success) {
              status.textContent = 'Lưu thành công.';
              status.style.color = 'green';
              setTimeout(() => { close(); }, 800);
            } else {
              status.textContent = 'Lưu thất bại: ' + (json.message || 'unknown');
              status.style.color = 'red';
            }
          } catch (err) {
            status.textContent = 'Lỗi: ' + err.message;
            status.style.color = 'red';
          }
        });
      }
      
  
    // ---------- CHANGE TYPE (option_choice) MODAL (auto-detect group and allow edit) ----------
    function showChangeTypeModal(container, partIndex, idTest) {
      // gather nearby text to parse range
      const ctxText = (container.innerText || '') + '\n' +
                      (container.previousElementSibling ? container.previousElementSibling.innerText : '') + '\n' +
                      (container.nextElementSibling ? container.nextElementSibling.innerText : '');
      const parsed = parseRangeFromText(ctxText);
  
      const overlay = el('div', { class: 'dev-change-type-overlay', style: {
        position: 'fixed', left: '0', top: '0', right: '0', bottom: '0',
        display: 'flex', alignItems: 'center', justifyContent: 'center',
        zIndex: '99999', background: 'rgba(0,0,0,0.3)'
      }});
  
      const box = el('div', { class: 'dev-change-type-box', style: {
        minWidth: '320px', maxWidth: '720px', padding: '16px', borderRadius: '8px',
        background: '#fff', boxShadow: '0 6px 24px rgba(0,0,0,0.2)'
      }});
  
      const title = el('h3', {}, [`Đổi Type / Thêm option_choice (Part ${partIndex}, Test ${idTest})`]);
      const info = el('div', { style: { marginBottom: '8px', fontSize: '13px', color: '#333' } }, [
        'Hệ thống sẽ cố gắng lấy group (ví dụ "1-6"). Bạn có thể chỉnh lại trước khi lưu. Nhập các option cách nhau bởi dấu phẩy (VD: A,B,C,D).'
      ]);
  
      // group inputs (start-end) with ability to edit
      const groupRow = el('div', { style: { display: 'flex', gap: '8px', marginBottom: '8px' }});
      const startInput = el('input', { type: 'number', placeholder: 'start', style: { flex: '1', padding: '8px' }});
      const endInput   = el('input', { type: 'number', placeholder: 'end', style: { flex: '1', padding: '8px' }});
      if (parsed) {
        startInput.value = parsed.start;
        endInput.value = parsed.end;
      }
      groupRow.appendChild(startInput);
      groupRow.appendChild(endInput);
  
      // option input
      const inputOptions = el('input', {
        type: 'text',
        placeholder: 'A,B,C,D',
        style: { width: '100%', padding: '8px', marginBottom: '8px' }
      });
  
      const btnRow = el('div', { style: { display: 'flex', justifyContent: 'flex-end', gap: '8px' }});
      const cancelBtn = el('button', { type: 'button', style: { padding: '8px 12px' } }, 'Cancel');
      const saveBtn = el('button', { type: 'button', style: { padding: '8px 12px' } }, 'Save');
  
      const status = el('div', { style: { marginTop: '8px', fontSize: '13px' } }, '');
  
      btnRow.appendChild(cancelBtn);
      btnRow.appendChild(saveBtn);
  
      box.appendChild(title);
      box.appendChild(info);
      box.appendChild(groupRow);
      box.appendChild(inputOptions);
      box.appendChild(btnRow);
      box.appendChild(status);
      overlay.appendChild(box);
      document.body.appendChild(overlay);
  
      function close() { overlay.remove(); }
      cancelBtn.addEventListener('click', close);
  
      saveBtn.addEventListener('click', async function() {
        const s = startInput.value.trim();
        const e = endInput.value.trim();
        if (s === '' || e === '') {
          status.textContent = 'Vui lòng nhập start và end.';
          status.style.color = 'red';
          return;
        }
        const sInt = parseInt(s, 10);
        const eInt = parseInt(e, 10);
        if (!Number.isInteger(sInt) || !Number.isInteger(eInt) || sInt <= 0 || eInt <= 0 || eInt < sInt) {
          status.textContent = 'Start/End không hợp lệ (end >= start).';
          status.style.color = 'red';
          return;
        }
  
        const raw = inputOptions.value.trim();
        if (!raw) {
          status.textContent = 'Vui lòng nhập option (VD: A,B,C,D)';
          status.style.color = 'red';
          return;
        }
        // normalize option string and array
        const optionArr = raw.split(',').map(s => s.trim()).filter(Boolean);
        if (optionArr.length === 0) {
          status.textContent = 'Option không hợp lệ.';
          status.style.color = 'red';
          return;
        }
        // We'll send option_choice as comma-separated string (server expects that in earlier PHP)
        const optionChoiceStr = optionArr.join(',');
  
        status.textContent = 'Đang gửi...';
        status.style.color = '#333';
  
        try {
          const resp = await fetch(`${siteUrl}/api/v1/dev/ielts/reading_listening/update-new-type`, {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({
              idTest: idTest,
              partIndex: partIndex,
              typeRequest: 'ielts_reading',
              range: { start: String(sInt), end: String(eInt) },
              option_choice: optionChoiceStr
            })
          });
  
          if (!resp.ok) {
            const txt = await resp.text();
            throw new Error('Server trả lỗi: ' + resp.status + ' ' + txt);
          }
          const json = await resp.json();
          if (json.success) {
            status.textContent = 'Lưu thành công.';
            status.style.color = 'green';
            setTimeout(() => { close(); }, 700);
          } else {
            status.textContent = 'Lưu thất bại: ' + (json.message || 'unknown');
            status.style.color = 'red';
          }
        } catch (err) {
          status.textContent = 'Lỗi: ' + err.message;
          status.style.color = 'red';
        }
      });
    }
  
    // attach hover overlays to group-containers
    function attachHover(partIndex, idTest) {
      const containers = document.querySelectorAll('.group-container');
      containers.forEach(container => {
        // ensure relative positioning for absolute overlay
        const cs = window.getComputedStyle(container);
        if (cs.position === 'static' || !cs.position) {
          container.style.position = 'relative';
        }
  
        // create overlay (hidden by default)
        let hoverOverlay = container.querySelector('.dev-hover-overlay');
        if (!hoverOverlay) {
          hoverOverlay = el('div', { class: 'dev-hover-overlay', style: {
            position: 'absolute',
            left: '50%',
            top: '50%',
            transform: 'translate(-50%,-50%)',
            display: 'none',
            zIndex: '9999',
            pointerEvents: 'auto',
            textAlign: 'center'
          }});
  
          // Đổi type button (now active)
          const btnType = el('button', { type: 'button', class: 'dev-btn-type', style: {
            display: 'block',
            marginBottom: '8px',
            padding: '8px 12px',
            borderRadius: '6px',
            border: '1px solid #1e87f0',
            background: '#1e87f0',
            color: '#fff',
            cursor: 'pointer'
          }}, 'Đổi type / Thêm option');
  
          // Add category button
          const btnAddCat = el('button', { type: 'button', class: 'dev-btn-add-cat', style: {
            display: 'block',
            padding: '8px 12px',
            borderRadius: '6px',
            border: '1px solid #1e87f0',
            background: '#1e87f0',
            color: '#fff',
            cursor: 'pointer'
          }}, 'Thêm category');
  
          hoverOverlay.appendChild(btnType);
          hoverOverlay.appendChild(btnAddCat);
          container.appendChild(hoverOverlay);
  
          // click handlers
          btnAddCat.addEventListener('click', function(e) {
            e.stopPropagation();
            showAddCategoryModal(container, partIndex, idTest);
          });
          btnType.addEventListener('click', function(e) {
            e.stopPropagation();
            showChangeTypeModal(container, partIndex, idTest);
          });
        }
  
        // show/hide on mouseenter/mouseleave
        container.addEventListener('mouseenter', function() {
          hoverOverlay.style.display = 'block';
        });
        container.addEventListener('mouseleave', function() {
          hoverOverlay.style.display = 'none';
        });
      });
    }
  
    // exported devOnMode
    window.devOnMode = function(partIndex, idTest) {
      try {
        partIndex = parseInt(partIndex, 10);
        idTest = parseInt(idTest, 10);
      } catch (e) {}
      console.log("devOnMode called. Part:", partIndex, "idTest:", idTest);
      attachHover(partIndex, idTest);
      // you can re-call devOnMode later with other params if needed
    };
  
  })();
  