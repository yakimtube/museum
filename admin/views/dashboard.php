<?php
require_once 'includes/stats.php';
$stats = new VisitorStats($conn);
?>
<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 mb-8">
    <!-- Stats Cards -->
    <div class="bg-white rounded-lg shadow-md p-6">
        <h3 class="text-lg font-semibold mb-2">Today's Visitors</h3>
        <p class="text-3xl font-bold text-blue-600"><?php echo $stats->getTodayVisitors(); ?></p>
    </div>
    
    <div class="bg-white rounded-lg shadow-md p-6">
        <h3 class="text-lg font-semibold mb-2">Active Access Codes</h3>
        <p class="text-3xl font-bold text-green-600"><?php echo $stats->getActiveAccessCodes(); ?></p>
    </div>
    
    <div class="bg-white rounded-lg shadow-md p-6">
        <h3 class="text-lg font-semibold mb-2">Total Exhibits</h3>
        <p class="text-3xl font-bold text-purple-600"><?php echo $stats->getTotalExhibits(); ?></p>
    </div>
</div>

<!-- Exhibits Management -->
<div class="bg-white rounded-lg shadow-md p-6 mb-8">
    <div class="flex justify-between items-center mb-6">
        <h2 class="text-xl font-bold">Manage Exhibits</h2>
        <button onclick="showAddExhibitModal()" 
                class="bg-blue-600 text-white px-4 py-2 rounded-md hover:bg-blue-700">
            Add New Exhibit
        </button>
    </div>
    
    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200">
            <thead>
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">ID</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Title</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Languages</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Media</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                <?php foreach ($stats->getExhibits() as $exhibit): ?>
                <tr>
                    <td class="px-6 py-4 whitespace-nowrap"><?php echo htmlspecialchars($exhibit['id']); ?></td>
                    <td class="px-6 py-4"><?php echo htmlspecialchars($exhibit['title']); ?></td>
                    <td class="px-6 py-4">
                        <?php 
                        $languages = explode(',', $exhibit['languages']);
                        foreach ($languages as $lang): ?>
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800 mr-1">
                                <?php echo htmlspecialchars($lang); ?>
                            </span>
                        <?php endforeach; ?>
                    </td>
                    <td class="px-6 py-4">
                        <div class="flex space-x-2">
                            <?php if (!empty($exhibit['has_image'])): ?>
                                <span class="text-green-600" title="Has Image">📷</span>
                            <?php endif; ?>
                            <?php if (!empty($exhibit['has_audio'])): ?>
                                <span class="text-blue-600" title="Has Audio">🎵</span>
                            <?php endif; ?>
                        </div>
                    </td>
                    <td class="px-6 py-4">
                        <button onclick="editExhibit('<?php echo $exhibit['id']; ?>')"
                                class="text-indigo-600 hover:text-indigo-900 mr-3">Edit</button>
                        <button onclick="deleteExhibit('<?php echo $exhibit['id']; ?>')"
                                class="text-red-600 hover:text-red-900">Delete</button>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<!-- Add/Edit Exhibit Modal -->
<div id="exhibitModal" class="hidden fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full">
    <div class="relative top-20 mx-auto p-5 border w-full max-w-4xl shadow-lg rounded-md bg-white">
        <div class="flex justify-between items-center mb-4">
            <h3 class="text-xl font-bold" id="modalTitle">Add New Exhibit</h3>
            <button onclick="closeModal()" class="text-gray-600 hover:text-gray-900">&times;</button>
        </div>
        
        <form id="exhibitForm" action="handlers/exhibit_handler.php" method="POST" enctype="multipart/form-data">
            <input type="hidden" name="action" id="formAction" value="add">
            <input type="hidden" name="exhibit_id" id="exhibitId">
            
            <div class="space-y-6">
                <div>
                    <label class="block text-sm font-medium text-gray-700">Exhibit ID</label>
                    <input type="text" name="id" required 
                           pattern="[A-Za-z0-9-_]+"
                           title="Only letters, numbers, hyphens, and underscores allowed"
                           class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
                    <p class="mt-1 text-sm text-gray-500">
                        This ID will be used in QR codes and URLs. Use only letters, numbers, hyphens, and underscores.
                    </p>
                </div>
                
                <?php foreach ($languages as $code => $name): ?>
                <div class="border-t pt-6">
                    <h4 class="font-medium mb-4"><?php echo $name; ?> Content</h4>
                    
                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Title</label>
                            <input type="text" name="title_<?php echo $code; ?>" required 
                                   class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Description</label>
                            <textarea name="description_<?php echo $code; ?>" required 
                                      rows="4"
                                      class="mt-1 block w-full rounded-md border-gray-300 shadow-sm"></textarea>
                            <p class="mt-1 text-sm text-gray-500">
                                Provide a detailed description that will be displayed to visitors.
                                You can use basic HTML tags for formatting.
                            </p>
                        </div>
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Image</label>
                                <input type="file" name="image_<?php echo $code; ?>" 
                                       accept="image/*"
                                       data-preview="preview-image-<?php echo $code; ?>"
                                       class="mt-1 block w-full">
                                <div id="preview-image-<?php echo $code; ?>" class="mt-2"></div>
                            </div>
                            
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Audio Guide</label>
                                <input type="file" name="audio_<?php echo $code; ?>" 
                                       accept="audio/*"
                                       data-preview="preview-audio-<?php echo $code; ?>"
                                       class="mt-1 block w-full">
                                <div id="preview-audio-<?php echo $code; ?>" class="mt-2"></div>
                            </div>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
            
            <div class="mt-6 flex justify-end space-x-3">
                <button type="button" onclick="closeModal()"
                        class="bg-gray-200 text-gray-700 py-2 px-4 rounded-md hover:bg-gray-300">
                    Cancel
                </button>
                <button type="submit" 
                        class="bg-blue-600 text-white py-2 px-4 rounded-md hover:bg-blue-700">
                    Save Exhibit
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Analytics Charts -->
<div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
    <div class="bg-white rounded-lg shadow-md p-6">
        <h2 class="text-xl font-bold mb-4">Popular Exhibits</h2>
        <canvas id="popularExhibitsChart"></canvas>
    </div>
    
    <div class="bg-white rounded-lg shadow-md p-6">
        <h2 class="text-xl font-bold mb-4">Language Distribution</h2>
        <canvas id="languageChart"></canvas>
    </div>
</div>

<script src="js/admin/charts.js"></script>
<script src="js/admin/exhibits.js"></script>