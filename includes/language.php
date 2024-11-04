<?php
function getTranslations($lang) {
    $translations = [
        'en' => [
            'welcome' => 'Welcome to Museum Audio Guide',
            'select_language' => 'Select Language',
            'enter_code' => 'Enter Access Code',
            'continue' => 'Continue',
            'invalid_code' => 'Invalid access code',
            'enter_exhibit' => 'Enter exhibit ID',
            'connect_headphones' => 'Please connect headphones to listen to the audio guide'
        ],
        'fr' => [
            'welcome' => 'Bienvenue au Guide Audio du Musée',
            'select_language' => 'Sélectionnez la langue',
            'enter_code' => 'Entrez le code d\'accès',
            'continue' => 'Continuer',
            'invalid_code' => 'Code d\'accès invalide',
            'enter_exhibit' => 'Entrez l\'ID de l\'exposition',
            'connect_headphones' => 'Veuillez connecter un casque pour écouter le guide audio'
        ],
        // Add more languages as needed
    ];
    
    return $translations[$lang] ?? $translations['en'];
}