<?php
if (!isset($_SESSION['access_verified'])) {
    header('Location: index.php');
    exit();
}
?>
<div class="container mx-auto px-4 py-8">
    <div class="max-w-xl mx-auto">
        <form id="exhibitForm" class="mb-8">
            <div class="flex gap-4">
                <input type="text" id="exhibitId" 
                       placeholder="Enter exhibit ID" 
                       class="flex-1 rounded-md border-gray-300 shadow-sm focus:border-indigo-300">
                <button type="submit" 
                        class="bg-blue-600 text-white px-6 py-2 rounded-md hover:bg-blue-700">
                    View
                </button>
            </div>
        </form>

        <div id="exhibitContent" class="bg-white rounded-lg shadow-md p-6 hidden">
            <h2 id="exhibitTitle" class="text-2xl font-bold mb-4"></h2>
            <div id="exhibitImage" class="mb-4"></div>
            <p id="exhibitDescription" class="mb-4"></p>
            
            <div id="audioSection" class="hidden">
                <audio id="exhibitAudio" controls class="w-full"></audio>
            </div>
            
            <div id="noHeadphonesWarning" class="bg-yellow-100 p-4 rounded-md hidden">
                <p class="text-yellow-800">Please connect headphones to listen to the audio guide.</p>
            </div>
        </div>
    </div>
</div>