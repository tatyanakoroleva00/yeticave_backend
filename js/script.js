'use strict';

flatpickr('#lot-date', {
  enableTime: false,
  dateFormat: "Y-m-d",
  locale: "ru"
});
function removeFile() {
  fetch('add.php', {method: POST})
}
