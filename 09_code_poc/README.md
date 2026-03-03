# 09 — Implémentation minimale du POC

## Fonctionnalité métier livrée
- Consultation du catalogue actif.
- Calcul du total panier avec remise commerciale (>100€ => -10%).

## Conventions de nommage
- Classes : `PascalCase` (`ProductService`).
- Méthodes : `camelCase` (`calculateCartTotal`).
- Dossiers MVC : `models/`, `services/`, `controllers/`, `views/`.

## Architecture MVC
- **Model** : `models/Product.php`
- **Service métier** : `services/ProductService.php`
- **Controller** : `controllers/ProductController.php`
- **View documentaire** : `views/catalogue.md`

## Intégration pipeline
Le fichier `03_pipeline.yml` valide explicitement la présence des fichiers MVC et exécute les tests unitaires (`ProductServiceTest.php`) dans l’étape `unit_tests`.
