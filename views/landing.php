<?php
$languages = ['en' => 'English', 'fr' => 'Français', 'es' => 'Español', 'ar' => 'العربية'];
?>
<div class="min-h-screen flex items-center justify-center">
    <div class="bg-white p-8 rounded-lg shadow-md w-full max-w-md">
        <h1 class="text-2xl font-bold mb-6 text-center">Welcome to Museum Audio Guide</h1>
        
        <form action="handlers/language_handler.php" method="POST" class="space-y-4">
            <div>
                <label class="block text-sm font-medium text-gray-700">Select Language</label>
                <select name="language" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300">
                    <?php foreach ($languages as $code => $name): ?>
                        <option value="<?php echo $code; ?>"><?php echo $name; ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            
            <div>
                <label class="block text-sm font-medium text-gray-700">Access Code</label>
                <input type="text" name="access_code" required 
                       class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300">
            </div>
            
            <button type="submit" 
                    class="w-full bg-blue-600 text-white py-2 px-4 rounded-md hover:bg-blue-700">
                Continue
            </button>
        </form>
    </div>
</div>