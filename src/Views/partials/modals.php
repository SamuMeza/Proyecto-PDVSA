<div class="modal-overlay" id="modalOverlay" style="display:none" onclick="closeModal()"></div>
<div class="modal" id="genericModal" style="display:none">
    <div class="modal-header">
        <h3 id="modalTitle">Confirmar</h3>
        <button class="modal-close" onclick="closeModal()">&times;</button>
    </div>
    <div class="modal-body" id="modalBody">
        <p id="modalMessage">¿Está seguro?</p>
    </div>
    <div class="modal-footer">
        <button class="btn btn-secondary" onclick="closeModal()">Cancelar</button>
        <button class="btn btn-primary" id="modalConfirmBtn">Confirmar</button>
    </div>
</div>
<script>
function openModal(title, message, onConfirm) {
    document.getElementById('modalTitle').textContent = title;
    document.getElementById('modalMessage').textContent = message;
    document.getElementById('modalOverlay').style.display = 'block';
    document.getElementById('genericModal').style.display = 'block';
    document.getElementById('modalConfirmBtn').onclick = function() {
        closeModal();
        if (onConfirm) onConfirm();
    };
}
function closeModal() {
    document.getElementById('modalOverlay').style.display = 'none';
    document.getElementById('genericModal').style.display = 'none';
}
</script>
