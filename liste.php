<?php require('bdd.php'); 

$select_categories = $connexion->query('SELECT * FROM category');
$categories = $select_categories->fetchAll();

$errors = [];

//Erreur si aucun champ n'est rempli ou si le seul champ rempli est 'categorie' avec la valeur 'all'
if ((empty($_POST['product_name']) && empty($_POST['category']) && empty($_POST['max-price'])) || (empty($_POST['product_name']) && empty($_POST['max-price']) && $_POST['category'] == 'all')) {
    $errors[] = 'aucun champ remplli';
}

//Requête en fonction des inputs remplies
if(empty($errors)) {
    $first_condition = true;
    $request ='SELECT * FROM products INNER JOIN category ON products.category = category.id WHERE';
    //Concatenation
    if(!empty($_POST['product_name'])) {
        if(!$first_condition) {
            $request .= ' AND';
        }else{
            $first_condition = false;
        }
        $request .= ' name LIKE :product_name';
    }
    if(!empty($_POST['max-price'])) {
        if(!$first_condition) {
            $request .= ' AND';
        }else{
            $first_condition = false;
        }
        $request .= ' price <= :price';
    }
    if($_POST['category'] != 'all') {
        if(!$first_condition) {
            $request .= ' AND';
        }else{
            $first_condition = false;
        }
        $request .= ' products.category = :category';
    }
    //bindValue
    $select_products = $connexion->prepare($request);
    if(!empty($_POST['product_name'])) {
        $select_products->bindValue(':product_name', '%'.htmlspecialchars($_POST['product_name']).'%');
    }
    if($_POST['category'] != 'all') {
        $select_products->bindValue(':category', htmlspecialchars($_POST['category']));
    }
    if(!empty($_POST['max-price'])) {
        $select_products->bindValue(':price', htmlspecialchars($_POST['max-price']));
    }
    $select_products->execute();
    $products = $select_products->fetchAll();
    

}

?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8" />
    <title>Recherche d'article</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css" integrity="sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm" crossorigin="anonymous">
    <link href="https://fonts.googleapis.com/css?family=Nunito" rel="stylesheet">
    <link rel="stylesheet" type="text/css" media="screen" href="assets/css/liste.css" />
    <link rel="stylesheet" type="text/css" media="screen" href="assets/css/app.css" />
</head>
<body>  
    <?php include('header.php'); ?>
    <section class="container search-product d-flex justify-content-center">
        <div class="col-md-5">
            <div class="card my-3">
                <h2 class="card-header">Rechercher un article</h2>
            <!-- Formulaire de recherche -->
                <form method="post" action="liste.php" class="py-4 px-5">
                    <div class="form-group">
                        <label for="product_name">Nom du produit :</label>
                        <input name="product_name" class="form-control" type="text" >
                    </div>
                    <div class="form-group">
                        <label for="category">Catégorie :</label>
                        <select class="form-control" name="category">
                        <option value="all">--- Tous les articles ---</option>
                            <?php
                            foreach($categories as $category) {
                                ?>
                                <option value="<?= $category['id'] ?>"><?= $category['category'] ?></option>
                                <?php
                            }
                            ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="max-price">Prix maximum</label>        
                        <input class="form-control" name="max-price" type="number" min="0">
                    </div>
                    <button class="btn btn-primary">Rechercher</button>
                </form>
            </div>
        </div>
    </section>

<section class="container">
    <div class="card">
        <div class="card-header py-3 px-5">
            <h4 class='no-margin'><?php echo count($products); ?> résultat<?php if(count($products) > 1){echo 's';} ?></h4>
        </div>
        <div class="card-body p-5 d-flex flex-column">
            <?php
                foreach ($products as $product) {
                    ?>
                    <div class="d-flex flex-row my-4">
                        <div class="col-4"><img class="img-product" src="assets/img/<?= $product['photo'] ?>" alt="photo du produit"></div>
                        <div class="col-6 p-5">
                            <h3><?= $product['name'] ?></h3>
                            <small><?= $product['category'] ?></small>
                            
                        </div>
                        <div class="col-2 d-flex flex-column justify-content-center">
                            <span class="price"><?= $product['price'] ?> €</span>
                        </div>
                    </div>
                    <?php
                }
            ?>
        </div>
    </div>
</section>

<?php include('footer.php'); ?>

<script src="https://code.jquery.com/jquery-3.2.1.slim.min.js" integrity="sha384-KJ3o2DKtIkvYIK3UENzmM7KCkRr/rE9/Qpg6aAZGJwFDMVNA/GpGFF93hXpG5KkN" crossorigin="anonymous"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.12.9/umd/popper.min.js" integrity="sha384-ApNbgh9B+Y1QKtv3Rn7W3mgPxhU9K/ScQsAP7hUibX39j7fakFPskvXusvfa0b4Q" crossorigin="anonymous"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.min.js" integrity="sha384-JZR6Spejh4U02d8jOt6vLEHfe/JQGiRRSQQxSfFWpi1MquVdAyjUar5+76PVCmYl" crossorigin="anonymous"></script>
</body>
</html>