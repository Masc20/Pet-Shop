<?php
require_once 'models/Pet.php';
require_once 'core/Helpers.php'; // Include Helpers for search functions

class PetController extends Controller {
    public function index() {
        $petModel = new Pet();
        
        // Get current page from URL, default to 1
        $currentPage = isset($_GET['page']) ? (int)$_GET['page'] : 1;
        // Get filter parameters from URL
        $filterQuery = $_GET['query'] ?? '';
        $filterType = $_GET['type'] ?? '';
        
        $petsPerPage = 12; // Number of pets per page
        $offset = ($currentPage - 1) * $petsPerPage;
        
        // Get filtered and paginated pets
        $pets = $petModel->getPetsWithFiltersAndPagination($filterQuery, $filterType, $petsPerPage, $offset);
        // Get total count of pets matching filters
        $totalPets = $petModel->getTotalPetsWithFilters($filterQuery, $filterType);
        
        // Calculate total pages
        $totalPages = ceil($totalPets / $petsPerPage);
        
        // Pass data to the view
        $this->view('pets/index', [
            'pets' => $pets,
            'currentPage' => $currentPage,
            'totalPages' => $totalPages,
            'filterQuery' => $filterQuery,
            'filterType' => $filterType
        ]);
    }
    
    public function show($id) {
        $petModel = new Pet();
        $pet = $petModel->getById($id);
        
        if (!$pet) {
            $this->redirect('/pets');
            return;
        }
        
        $this->view('pets/show', [
            'pet' => $pet
        ]);
    }
    
    public function adopt() {
        if (!isLoggedIn()) {
            $this->redirect('/login');
            return;
        }
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $petId = $_POST['pet_id'];
            $userId = $_SESSION['user_id'];
            
            $petModel = new Pet();
            if ($petModel->adopt($petId, $userId)) {
                $_SESSION['success'] = 'Pet adopted successfully!';
                $this->redirect('/adopted-pets');
            } else {
                $_SESSION['error'] = 'Failed to adopt pet.';
                $this->redirect('/pet/' . $petId);
            }
        }
    }
    
    public function adoptedPets() {
        $petModel = new Pet();
        $adoptedPets = $petModel->getAdoptedPets();
        
        $this->view('pets/adopted', [
            'adoptedPets' => $adoptedPets
        ]);
    }
}
?>
