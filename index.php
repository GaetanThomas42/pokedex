<?php

require_once __DIR__ . '/vendor/autoload.php';

use App\Controller\DraftController;
use App\Controller\HomeController;

$action = isset($_GET['action']) ? $_GET['action'] : 'home_page';
$id = $_GET['id'] ?? null;

$draftController = new DraftController();
$homeController = new HomeController();

switch ($action) {
    case 'home_page':
        $homeController->homePage();
        break;
    case 'start':
        $draftController->startDraft();
        break;
    case 'pick':
        $draftController->pickPokemon($id);
        break;
    case 'confirm_pick':
        $draftController->confirmPick($id);
        break;
    case 'drafts':
        $homeController->drafts();
        break;
    default:
        $homeController->homePage();
}
