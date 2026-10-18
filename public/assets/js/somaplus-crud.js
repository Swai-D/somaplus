/* SomaPlus - front-end behaviour for the Dream-style add / edit / delete modals.
   Edit modals are filled from the table row that opened them (Bootstrap relatedTarget);
   delete removes the row (demo only). Wire the forms / delete button to your backend. */
(function () {
  var current = null;
  function text(td) {
    if (!td) return "";
    var t = td.querySelector("h6, .fw-semibold, .fw-medium, a");
    return (t ? t.textContent : td.textContent).replace(/\s+/g, " ").trim();
  }
  function rowOf(ev) { return ev.relatedTarget ? ev.relatedTarget.closest("tr") : null; }
  document.addEventListener("DOMContentLoaded", function () {
    if (window.flatpickr) document.querySelectorAll(".sp-date").forEach(function (el) { flatpickr(el, {dateFormat: "d M Y", allowInput: true}); });
    var edit = document.getElementById("edit-modal");
    if (edit) edit.addEventListener("show.bs.modal", function (ev) {
      var row = rowOf(ev); if (!row) return;
      var cells = row.children;
      edit.querySelectorAll("[data-col]").forEach(function (el) {
        var v = text(cells[+el.dataset.col]);
        if (el._flatpickr) el._flatpickr.setDate(v, false, "d M Y"); else el.value = v;
      });
      var st = edit.querySelector("[data-sp-status]");
      if (st) st.checked = /^(active|paid|approved|accepted|completed)/i.test(text(cells[+st.dataset.spStatus]));
    });
    var delModal = document.getElementById("delete-modal");
    if (delModal) {
      delModal.addEventListener("show.bs.modal", function (ev) { current = rowOf(ev); });
      var del = document.getElementById("sp-confirm-delete");
      if (del) del.addEventListener("click", function () {
        if (current) { var r = current; r.style.transition = "opacity .25s"; r.style.opacity = 0; setTimeout(function () { r.remove(); }, 250); current = null; }
        bootstrap.Modal.getInstance(delModal).hide();
      });
    }
    document.querySelectorAll("form[data-sp-form]").forEach(function (f) {
      f.addEventListener("submit", function (e) { e.preventDefault(); var m = f.closest(".modal"); if (m) bootstrap.Modal.getInstance(m).hide(); });
    });
  });
})();
