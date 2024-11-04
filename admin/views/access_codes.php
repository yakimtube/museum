<?php
if (!isset($_SESSION['admin_logged_in'])) {
    header('Location: index.php');
    exit();
}
?>
<div class="bg-white rounded-lg shadow-md p-6">
    <div class="flex justify-between items-center mb-6">
        <h2 class="text-xl font-bold">Access Code Management</h2>
        <button onclick="showGenerateModal()" 
                class="bg-blue-600 text-white px-4 py-2 rounded-md hover:bg-blue-700">
            Generate Codes
        </button>
    </div>
    
    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200">
            <thead>
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Code</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Valid From</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Valid Until</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                </tr>
            </thead>
            <tbody id="accessCodesList" class="bg-white divide-y divide-gray-200">
                <!-- Populated by JavaScript -->
            </tbody>
        </table>
    </div>
</div>

<!-- Generate Codes Modal -->
<div id="generateModal" class="hidden fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full">
    <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
        <div class="flex justify-between items-center mb-4">
            <h3 class="text-xl font-bold">Generate Access Codes</h3>
            <button onclick="closeGenerateModal()" class="text-gray-600 hover:text-gray-900">&times;</button>
        </div>
        
        <form id="generateForm" class="space-y-4">
            <div>
                <label class="block text-sm font-medium text-gray-700">Quantity</label>
                <input type="number" name="quantity" min="1" max="100" required 
                       class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
            </div>
            
            <div>
                <label class="block text-sm font-medium text-gray-700">Valid From</label>
                <input type="datetime-local" name="valid_from" required 
                       class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
            </div>
            
            <div>
                <label class="block text-sm font-medium text-gray-700">Valid Until</label>
                <input type="datetime-local" name="valid_until" required 
                       class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
            </div>
            
            <button type="submit" 
                    class="w-full bg-blue-600 text-white py-2 px-4 rounded-md hover:bg-blue-700">
                Generate
            </button>
        </form>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    loadAccessCodes();
    setupGenerateForm();
});

function loadAccessCodes() {
    fetch('handlers/access_code_handler.php?action=list')
        .then(response => response.json())
        .then(codes => {
            const tbody = document.getElementById('accessCodesList');
            tbody.innerHTML = codes.map(code => `
                <tr>
                    <td class="px-6 py-4">${code.code}</td>
                    <td class="px-6 py-4">${new Date(code.valid_from).toLocaleString()}</td>
                    <td class="px-6 py-4">${new Date(code.valid_until).toLocaleString()}</td>
                    <td class="px-6 py-4">
                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                                   ${code.is_active ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800'}">
                            ${code.is_active ? 'Active' : 'Inactive'}
                        </span>
                    </td>
                    <td class="px-6 py-4">
                        ${code.is_active ? `
                            <button onclick="deactivateCode('${code.code}')"
                                    class="text-red-600 hover:text-red-900">
                                Deactivate
                            </button>
                        ` : ''}
                    </td>
                </tr>
            `).join('');
        });
}

function setupGenerateForm() {
    document.getElementById('generateForm').addEventListener('submit', function(e) {
        e.preventDefault();
        
        const formData = new FormData(this);
        formData.append('action', 'generate');
        
        fetch('handlers/access_code_handler.php', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                closeGenerateModal();
                loadAccessCodes();
            }
        });
    });
}

function deactivateCode(code) {
    if (confirm('Are you sure you want to deactivate this code?')) {
        const formData = new FormData();
        formData.append('action', 'deactivate');
        formData.append('code', code);
        
        fetch('handlers/access_code_handler.php', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                loadAccessCodes();
            }
        });
    }
}

function showGenerateModal() {
    document.getElementById('generateModal').classList.remove('hidden');
}

function closeGenerateModal() {
    document.getElementById('generateModal').classList.add('hidden');
    document.getElementById('generateForm').reset();
}
</script>