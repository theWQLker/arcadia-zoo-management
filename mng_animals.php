<?php
include 'header.php';
include 'config2.php';

if (!isset($_SESSION['loggedin']) && $_SESSION['role'] !== 'admin') {
    header('Location: login.php');
    exit;
}

// Handle Add, Update, Delete operations
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['add_animal']) || isset($_POST['edit_animal'])) {
        $prenom = $_POST['prenom'];
        $race = $_POST['race'];
        $habitat_id = $_POST['habitat_id'];
        $diet = $_POST['diet'];
        $description = $_POST['description'];
        $characteristics = $_POST['characteristics'];
        $species = $_POST['species'];
        $lifespan = $_POST['lifespan'];
        $size_weight = $_POST['size_weight'];
        $gestation = $_POST['gestation'];
        $natural_habitat = $_POST['natural_habitat'];
        $native_region = $_POST['native_region'];
        $lifestyle = $_POST['lifestyle'];
        $distinctive_features = $_POST['distinctive_features'];

        // Handle image upload
        $image = null;
        if (isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
            $image = file_get_contents($_FILES['image']['tmp_name']);
        }

        if (isset($_POST['add_animal'])) {
            $query = "INSERT INTO animals (prenom, race, habitat_id, diet, description, characteristics, image, species, lifespan, size_weight, gestation, natural_habitat, native_region, lifestyle, distinctive_features) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
            $stmt = $pdo->prepare($query);
            $stmt->execute([$prenom, $race, $habitat_id, $diet, $description, $characteristics, $image, $species, $lifespan, $size_weight, $gestation, $natural_habitat, $native_region, $lifestyle, $distinctive_features]);
        } elseif (isset($_POST['edit_animal'])) {
            $id = $_POST['id'];
            if ($image !== null) {
                $query = "UPDATE animals SET prenom = ?, race = ?, habitat_id = ?, diet = ?, description = ?, characteristics = ?, image = ?, species = ?, lifespan = ?, size_weight = ?, gestation = ?, natural_habitat = ?, native_region = ?, lifestyle = ?, distinctive_features = ? WHERE id = ?";
                $stmt = $pdo->prepare($query);
                $stmt->execute([$prenom, $race, $habitat_id, $diet, $description, $characteristics, $image, $species, $lifespan, $size_weight, $gestation, $natural_habitat, $native_region, $lifestyle, $distinctive_features, $id]);
            } else {
                $query = "UPDATE animals SET prenom = ?, race = ?, habitat_id = ?, diet = ?, description = ?, characteristics = ?, species = ?, lifespan = ?, size_weight = ?, gestation = ?, natural_habitat = ?, native_region = ?, lifestyle = ?, distinctive_features = ? WHERE id = ?";
                $stmt = $pdo->prepare($query);
                $stmt->execute([$prenom, $race, $habitat_id, $diet, $description, $characteristics, $species, $lifespan, $size_weight, $gestation, $natural_habitat, $native_region, $lifestyle, $distinctive_features, $id]);
            }
        }
    } elseif (isset($_POST['delete_animal'])) {
        $id = $_POST['id'];
        $query = "DELETE FROM animals WHERE id = ?";
        $stmt = $pdo->prepare($query);
        $stmt->execute([$id]);
    }
}

// Fetch animal details for editing
$edit_animal = null;
if (isset($_POST['edit_animal_form'])) {
    $id = $_POST['id'];
    $query = "SELECT * FROM animals WHERE id = ?";
    $stmt = $pdo->prepare($query);
    $stmt->execute([$id]);
    $edit_animal = $stmt->fetch(PDO::FETCH_ASSOC);
}

// Fetch Animals from the database
$query = "SELECT a.*, h.name as habitat_name
          FROM animals a
          LEFT JOIN habitats h ON a.habitat_id = h.id";
$stmt = $pdo->query($query);
$animals = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Fetch Habitats for Dropdown in forms
$habitatQuery = "SELECT id, name FROM habitats";
$habitatStmt = $pdo->query($habitatQuery);
$habitats = $habitatStmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html>

<head>
    <title>Manage Animals</title>
    <link rel="stylesheet" type="text/css" href="ric6.css">
    <style>
        /* Hide the rows for animals beyond the first six */
        .hidden-animals {
            display: none;
        }
        
        /* Style for the dropdown to show remaining animals */
        .animal-dropdown {
            margin-top: 20px;
        }
    </style>
</head>

<body>
    <div class="container">
        <h1>Manage Animals</h1>

        <!-- Display a table showing animals -->
        <h2>Current Animals</h2>
        <table class="animals-table">
            <tr>
                <th>Name</th>
                <th>Species</th>
                <th>Habitat</th>
                <th>Actions</th>
            </tr>

            <?php 
            $count = 0; // Initialize counter to track number of animals displayed
            foreach ($animals as $animal) : 
                if ($count < 4): // Show only the first six animals
            ?>
                <tr>
                    <td><?php echo htmlspecialchars($animal['prenom']); ?></td>
                    <td><?php echo htmlspecialchars($animal['species']); ?></td>
                    <td><?php echo htmlspecialchars($animal['habitat_name']); ?></td>
                    <td>
                        <!-- Form for editing an animal -->
                        <form method="post" style="display: inline-block;">
                            <input type="hidden" name="id" value="<?php echo $animal['id']; ?>">
                            <button type="submit" name="edit_animal_form" class="edit-button">Edit</button>
                        </form>
                        <!-- Form for deleting an animal -->
                        <form method="post" style="display: inline-block;" onsubmit="return confirm('Are you sure you want to delete this animal?');">
                            <input type="hidden" name="id" value="<?php echo $animal['id']; ?>">
                            <button type="submit" name="delete_animal" class="delete-button">Delete</button>
                        </form>
                    </td>
                </tr>
            <?php 
                else: // For animals beyond the first six, hide them initially
            ?>
                <tr class="hidden-animals">
                    <td><?php echo htmlspecialchars($animal['prenom']); ?></td>
                    <td><?php echo htmlspecialchars($animal['species']); ?></td>
                    <td><?php echo htmlspecialchars($animal['habitat_name']); ?></td>
                    <td>
                        <form method="post" style="display: inline-block;">
                            <input type="hidden" name="id" value="<?php echo $animal['id']; ?>">
                            <button type="submit" name="edit_animal_form" class="edit-button">Edit</button>
                        </form>
                        <form method="post" style="display: inline-block;" onsubmit="return confirm('Are you sure you want to delete this animal?');">
                            <input type="hidden" name="id" value="<?php echo $animal['id']; ?>">
                            <button type="submit" name="delete_animal" class="delete-button">Delete</button>
                        </form>
                    </td>
                </tr>
            <?php 
                endif; 
                $count++; // Increment the counter after each animal row
            endforeach; 
            ?>
        </table>

        <!-- Dropdown list for remaining animals -->
        <div class="animal-dropdown">
            <label for="hidden-animals-dropdown">View More Animals:</label>
            <select id="hidden-animals-dropdown" onchange="showAnimal(this.value)">
                <option value="">Select Animal</option>
                <!-- Populate dropdown with animals after the first six -->
                <?php foreach (array_slice($animals, 6) as $animal): ?>
                    <option value="<?php echo htmlspecialchars($animal['id']); ?>"><?php echo htmlspecialchars($animal['prenom']); ?></option>
                <?php endforeach; ?>
            </select>
        </div>

        <script>
            /**
             * Function to show a selected hidden animal from the dropdown
             * Hides all previously hidden rows and reveals the selected one
             */
            function showAnimal(id) {
                // Hide all rows with class 'hidden-animals'
                document.querySelectorAll('.hidden-animals').forEach(function(row) {
                    row.style.display = 'none';
                });

                // Show the selected animal row
                if (id) {
                    const row = document.querySelector(`input[value="${id}"]`).closest('tr');
                    if (row) {
                        row.style.display = 'table-row';
                    }
                }
            }
        </script>

        <?php if ($edit_animal): ?>
            <!-- Edit Animal Form -->
            <h2>Edit Animal</h2>
            <form method="post" enctype="multipart/form-data" class="edit-animal-form">
                <input type="hidden" name="id" value="<?php echo htmlspecialchars($edit_animal['id']); ?>">
                <input type="text" name="prenom" placeholder="Name" required value="<?php echo htmlspecialchars($edit_animal['prenom']); ?>">
                <input type="text" name="race" placeholder="Class, Order, and Family" required value="<?php echo htmlspecialchars($edit_animal['race']); ?>">
                <select name="habitat_id" required>
                    <option value="">Select Habitat</option>
                    <?php foreach ($habitats as $habitat) : ?>
                        <option value="<?php echo $habitat['id']; ?>" <?php echo ($habitat['id'] == $edit_animal['habitat_id']) ? 'selected' : ''; ?>><?php echo htmlspecialchars($habitat['name']); ?></option>
                    <?php endforeach; ?>
                </select>
                <input type="text" name="diet" placeholder="Diet" value="<?php echo htmlspecialchars($edit_animal['diet']); ?>">
                <textarea name="description" placeholder="Description"><?php echo htmlspecialchars($edit_animal['description']); ?></textarea>
                <textarea name="characteristics" placeholder="Characteristics"><?php echo htmlspecialchars($edit_animal['characteristics']); ?></textarea>
                <input type="file" name="image" accept="image/*">
                <input type="text" name="species" placeholder="Species" value="<?php echo htmlspecialchars($edit_animal['species']); ?>">
                <input type="text" name="lifespan" placeholder="Lifespan" value="<?php echo htmlspecialchars($edit_animal['lifespan']); ?>">
                <input type="text" name="size_weight" placeholder="Size and Weight" value="<?php echo htmlspecialchars($edit_animal['size_weight']); ?>">
                <input type="text" name="gestation" placeholder="Gestation" value="<?php echo htmlspecialchars($edit_animal['gestation']); ?>">
                <input type="text" name="natural_habitat" placeholder="Natural Habitat" value="<?php echo htmlspecialchars($edit_animal['natural_habitat']); ?>">
                <input type="text" name="native_region" placeholder="Native Region" value="<?php echo htmlspecialchars($edit_animal['native_region']); ?>">
                <textarea name="lifestyle" placeholder="Lifestyle"><?php echo htmlspecialchars($edit_animal['lifestyle']); ?></textarea>
                <textarea name="distinctive_features" placeholder="Distinctive Features"><?php echo htmlspecialchars($edit_animal['distinctive_features']); ?></textarea>
                <button type="submit" name="edit_animal">Update Animal</button>
            </form>
        <?php else: ?>
            <!-- Add New Animal Form -->
            <h2>Add New Animal</h2>
            <form method="post" enctype="multipart/form-data" class="add-animal-form">
                <input type="text" name="prenom" placeholder="Name" required>
                <input type="text" name="race" placeholder="Class, Order, and Family" required>
                <select name="habitat_id" required>
                    <option value="">Select Habitat</option>
                    <?php foreach ($habitats as $habitat) : ?>
                        <option value="<?php echo $habitat['id']; ?>"><?php echo htmlspecialchars($habitat['name']); ?></option>
                    <?php endforeach; ?>
                </select>
                <input type="text" name="diet" placeholder="Diet">
                <textarea name="description" placeholder="Description"></textarea>
                <textarea name="characteristics" placeholder="Characteristics"></textarea>
                <input type="file" name="image" accept="image/*">
                <input type="text" name="species" placeholder="Species">
                <input type="text" name="lifespan" placeholder="Lifespan">
                <input type="text" name="size_weight" placeholder="Size and Weight">
                <input type="text" name="gestation" placeholder="Gestation">
                <input type="text" name="natural_habitat" placeholder="Natural Habitat">
                <input type="text" name="native_region" placeholder="Native Region">
                <textarea name="lifestyle" placeholder="Lifestyle"></textarea>
                <textarea name="distinctive_features" placeholder="Distinctive Features"></textarea>
                <button type="submit" name="add_animal">Add Animal</button>
            </form>
        <?php endif; ?>

    </div>
</body>

</html>
