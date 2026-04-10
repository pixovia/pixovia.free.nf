// Anti-devtools detection - redirect to google.com
(function() {
  'use strict';

  // Detect devtools open
  let devtools = {
    open: false,
    orientation: null
  };

  const threshold = 160;

  // Check triggered by contextmenu, keydown, resize
  function check() {
    if (window.outerHeight - window.innerHeight > threshold || 
        window.outerWidth - window.innerWidth > threshold) {
      handleOpen();
    }
  }

  function handleOpen() {
    if (!devtools.open) {
      devtools.open = true;
      console.log('Devtools detected!'); // Ironically logs then redirects
      window.location.href = 'https://google.com';
    }
  }

  // Event listeners
  document.addEventListener('contextmenu', handleOpen);
  document.addEventListener('keydown', function(e) {
    if (e.key === 'F12' || (e.ctrlKey && e.shiftKey && e.key === 'I')) {
      handleOpen();
    }
  });

  // Resize detection for devtools panel
  let widthDiff = window.outerWidth - window.innerWidth;
  setInterval(check, 500);

  // Right-click tracking
  document.addEventListener('mousedown', function(e) {
    if (e.button === 2) handleOpen();
  });

  // Block tracking prevention
  Object.defineProperty(navigator, 'webdriver', {
    get: () => undefined,
  });
})();

