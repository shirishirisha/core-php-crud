(function () {
  // Show / hide password
  document.querySelectorAll('.pw-toggle').forEach(function (btn) {
    btn.addEventListener('click', function () {
      var input = btn.previousElementSibling, show = input.type === 'password';
      input.type = show ? 'text' : 'password';
      btn.textContent = show ? 'Hide' : 'Show';
    });
  });

  // Delete confirmation dialog
  var dlg = document.getElementById('confirm');
  document.querySelectorAll('[data-delete]').forEach(function (b) {
    b.addEventListener('click', function () {
      document.getElementById('del-id').value = b.dataset.delete;
      document.getElementById('del-name').textContent = b.dataset.name;
      dlg.showModal();
    });
  });

  // Generic confirm + auto-submit helpers (admin users page)
  document.querySelectorAll('form[data-confirm]').forEach(function (f) {
    f.addEventListener('submit', function (e) { if (!confirm(f.dataset.confirm)) e.preventDefault(); });
  });
  document.querySelectorAll('[data-autosubmit]').forEach(function (s) {
    s.addEventListener('change', function () { s.form.submit(); });
  });

  // Friendly price hint (e.g. 9800000 -> ₹98 L)
  var price = document.querySelector('input[name=price]'), hint = document.getElementById('price-hint');
  function fmt(n) { return n >= 1e7 ? '₹' + (n / 1e7).toFixed(2) + ' Cr' : n >= 1e5 ? '₹' + (n / 1e5).toFixed(2) + ' L' : n > 0 ? '₹' + n.toLocaleString('en-IN') : ''; }
  if (price && hint) { price.addEventListener('input', function () { hint.textContent = fmt(parseFloat(price.value) || 0); }); price.dispatchEvent(new Event('input')); }

  // Toast auto-dismiss
  var t = document.querySelector('.toast');
  if (t) setTimeout(function () { t.classList.add('hide'); }, 3500);
})();
