/**
 * WP Time Slots Booking Form - Shared Admin Helpers
 */
(function (window, document) {
    'use strict';

    function fallbackCopy(text, onSuccess) {
        var ta = document.createElement('textarea');
        ta.value = text;
        ta.setAttribute('readonly', '');
        ta.style.position = 'fixed';
        ta.style.top = '0';
        ta.style.left = '-9999px';
        document.body.appendChild(ta);
        ta.focus();
        ta.select();
        try {
            if (document.execCommand('copy') && typeof onSuccess === 'function') {
                onSuccess();
            }
        } catch (err) {
            // Silently ignore copy errors in restricted contexts
        }
        document.body.removeChild(ta);
    }

    function showButtonFeedback(btn, copiedLabel) {
        if (!btn) {
            return;
        }
        var original = btn.getAttribute('data-wpts-orig') || btn.textContent;
        btn.setAttribute('data-wpts-orig', original);
        btn.textContent = copiedLabel || '\u2713 Copied!';
        if (btn._wptsCopyTimer) {
            window.clearTimeout(btn._wptsCopyTimer);
        }
        btn._wptsCopyTimer = window.setTimeout(function () {
            btn.textContent = original;
        }, 1800);
    }

    /**
     * Copy arbitrary text to the clipboard with visual button feedback.
     *
     * @param {string} text
     * @param {HTMLElement|null} btn
     * @param {string} [copiedLabel]
     */
    window.wptsCopyText = function (text, btn, copiedLabel) {
        if (typeof text !== 'string') {
            text = String(text || '');
        }
        var onDone = function () {
            showButtonFeedback(btn, copiedLabel);
        };

        if (navigator.clipboard && window.isSecureContext && navigator.clipboard.writeText) {
            navigator.clipboard.writeText(text).then(onDone).catch(function () {
                fallbackCopy(text, onDone);
            });
        } else {
            fallbackCopy(text, onDone);
        }
    };

    /**
     * Copy trimmed text content of an element by ID.
     *
     * @param {string} elementId
     * @param {HTMLElement|null} btn
     * @param {string} [copiedLabel]
     */
    window.wptsCopyElementText = function (elementId, btn, copiedLabel) {
        var el = document.getElementById(elementId);
        if (!el) {
            return;
        }
        var text = (el.textContent || el.innerText || '').trim();
        window.wptsCopyText(text, btn, copiedLabel);
    };
})(window, document);
