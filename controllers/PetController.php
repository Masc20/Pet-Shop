<?php
require_once 'models/Pet.php';

class PetController extends Controller {
    public function index() {
        $petModel = new Pet();
        $pets = $petModel->getAll();
        
        // Filter by type if specified
        $type = $_GET['type'] ?? null;
        if ($type && in_array($type, ['dogs', 'cats'])) {
            $pets = array_filter($pets, function($pet) use ($type) {
                return $pet['type'] === $type;
            });
        }
        
        $this->view('pets/index', [
            'pets' => $pets
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
