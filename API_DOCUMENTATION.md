# API Documentation - Les Fermes Safia

## Configuration

### Base de données
- **Type**: MySQL
- **Nom de la base**: fermesafia
- **Hôte**: 127.0.0.1
- **Port**: 3306

### Authentification
- **Package**: tymon/jwt-auth
- **Type de token**: Bearer JWT
- **Durée de validité**: 24 heures (configurable dans config/jwt.php)

---

## Tables de la base de données

### users
| Champ | Type | Description |
|-------|------|-------------|
| id | bigint | Clé primaire (auto-increment) |
| nom | string | Nom de l'utilisateur |
| prenom | string | Prénom de l'utilisateur |
| numero | string | Numéro de téléphone |
| role | enum | admin, superviseur, comptable (défaut: comptable) |
| email | string | Email unique |
| password | string | Mot de passe hashé |
| bloquer | boolean | Statut de blocage (défaut: false) |
| created_at | timestamp | Date de création |
| updated_at | timestamp | Date de modification |

### matiere_premieres
| Champ | Type | Description |
|-------|------|-------------|
| id | bigint | Clé primaire (auto-increment) |
| nom | string | Nom de la matière première |
| code | string | Code unique (généré automatiquement) |
| image | string | Image (nullable) |
| unite | string | Unité de mesure |
| created_at | timestamp | Date de création |
| updated_at | timestamp | Date de modification |

**Génération du code**: Format XXX-YY (3 premiers caractères du nom + '-' + 2 caractères aléatoires)

### aliments
| Champ | Type | Description |
|-------|------|-------------|
| id | bigint | Clé primaire (auto-increment) |
| nom | string | Nom de l'aliment |
| code | string(6) | Code unique (généré automatiquement) |
| created_at | timestamp | Date de création |
| updated_at | timestamp | Date de modification |

**Génération du code**: Format al-XXXX (préfixe 'al-' + 4 chiffres aléatoires)

### formules
| Champ | Type | Description |
|-------|------|-------------|
| id | bigint | Clé primaire (auto-increment) |
| nom | string | Nom de la formule |
| composant | json | Composants de la formule (format JSON) |
| created_at | timestamp | Date de création |
| updated_at | timestamp | Date de modification |

**Structure du champ composant**: Tableau JSON contenant les composants de la formule

### stock_aliments
| Champ | Type | Description |
|-------|------|-------------|
| id | bigint | Clé primaire (auto-increment) |
| aliment_id | bigint | Clé étrangère vers aliments (cascade delete) |
| code_stock | string(10) | Code unique du stock (généré automatiquement) |
| formule_id | bigint | Clé étrangère vers formules (cascade delete) |
| quantite_fabriquer | decimal(10,2) | Quantité fabriquée |
| created_at | timestamp | Date de création |
| updated_at | timestamp | Date de modification |

**Génération du code_stock**: Format stck-XXXXXX (préfixe 'stck-' + 6 chiffres aléatoires)

### historique_aliments
| Champ | Type | Description |
|-------|------|-------------|
| id | bigint | Clé primaire (auto-increment) |
| stock_aliment_id | bigint | Clé étrangère vers stock_aliments (cascade delete) |
| gerant_id | bigint | Clé étrangère vers users (cascade delete) |
| type | enum | Type de mouvement ('entree' ou 'sortie') |
| quantite | decimal(10,2) | Quantité du mouvement |
| date_mouvement | date | Date du mouvement |
| created_at | timestamp | Date de création |
| updated_at | timestamp | Date de modification |

### sites
| Champ | Type | Description |
|-------|------|-------------|
| id | bigint | Clé primaire (auto-increment) |
| nom | string | Nom du site |
| adresse | string | Adresse |
| longitude | decimal(10,7) | Longitude GPS |
| latitude | decimal(10,7) | Latitude GPS |
| longueur | decimal(10,2) | Longueur |
| largeur | decimal(10,2) | Largeur |
| gerant | bigint | Clé étrangère vers users (cascade delete) |
| created_at | timestamp | Date de création |
| updated_at | timestamp | Date de modification |

### fermes
| Champ | Type | Description |
|-------|------|-------------|
| id | bigint | Clé primaire (auto-increment) |
| nom | string | Nom de la ferme |
| idsite | bigint | Clé étrangère vers sites (cascade delete) |
| longitude | decimal(10,7) | Longitude GPS |
| latitude | decimal(10,7) | Latitude GPS |
| longueur | decimal(10,2) | Longueur |
| largeur | decimal(10,2) | Largeur |
| gerant | bigint | Clé étrangère vers users (cascade delete) |
| created_at | timestamp | Date de création |
| updated_at | timestamp | Date de modification |

### magasins
| Champ | Type | Description |
|-------|------|-------------|
| id | bigint | Clé primaire (auto-increment) |
| nom | string | Nom du magasin |
| idsite | bigint | Clé étrangère vers sites (cascade delete) |
| longitude | decimal(10,7) | Longitude GPS |
| latitude | decimal(10,7) | Latitude GPS |
| longueur | decimal(10,2) | Longueur |
| largeur | decimal(10,2) | Largeur |
| gerant | bigint | Clé étrangère vers users (cascade delete) |
| created_at | timestamp | Date de création |
| updated_at | timestamp | Date de modification |

### lots
| Champ | Type | Description |
|-------|------|-------------|
| id | bigint | Clé primaire (auto-increment) |
| magasin_id | bigint | Clé étrangère vers magasins (cascade delete) |
| code_lot | string(10) | Code unique du lot (généré automatiquement) |
| created_at | timestamp | Date de création |
| updated_at | timestamp | Date de modification |

**Génération du code_lot**: Format jjmmaa-XX (date + itération)

**Table pivot lot_matiere_premiere:**
| Champ | Type | Description |
|-------|------|-------------|
| lot_id | bigint | Clé étrangère vers lots (cascade delete) |
| matiere_premiere_id | bigint | Clé étrangère vers matiere_premieres (cascade delete) |
| quantite | decimal(10,2) | Quantité de la matière première dans le lot |

### mouvement_stocks
| Champ | Type | Description |
|-------|------|-------------|
| id | bigint | Clé primaire (auto-increment) |
| magasin_id | bigint | Clé étrangère vers magasins (cascade delete) |
| matiere_id | bigint | Clé étrangère vers matiere_premieres (cascade delete) |
| lot_id | bigint | Clé étrangère vers lots (cascade delete) |
| type | enum | Type de mouvement (sortie, entree) |
| quantite | decimal(10,2) | Quantité du mouvement |
| date_mouvement | date | Date du mouvement |
| gerant_id | bigint | Clé étrangère vers users (cascade delete) |
| observation | text | Observation (nullable) |
| created_at | timestamp | Date de création |
| updated_at | timestamp | Date de modification |

---

## Routes API

### Authentification

#### POST /api/login
Connexion utilisateur

**Body:**
```json
{
  "email": "user@example.com",
  "password": "password"
}
```

**Response (200):**
```json
{
  "access_token": "eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9...",
  "token_type": "bearer",
  "expires_in": 3600,
  "user": {
    "id": 1,
    "nom": "Doe",
    "prenom": "John",
    "numero": "771234567",
    "role": "admin",
    "email": "user@example.com",
    "bloquer": false
  }
}
```

**Response (401):** Unauthorized - Identifiants incorrects
**Response (403):** Account is blocked - Compte bloqué

---

### Routes protégées (auth:api requis)

#### GET /api/me
Obtenir les informations de l'utilisateur connecté

**Headers:**
```
Authorization: Bearer {token}
```

**Response (200):**
```json
{
  "id": 1,
  "nom": "Doe",
  "prenom": "John",
  "numero": "771234567",
  "role": "admin",
  "email": "user@example.com",
  "bloquer": false
}
```

#### POST /api/logout
Déconnexion

**Headers:**
```
Authorization: Bearer {token}
```

**Response (200):**
```json
{
  "message": "Successfully logged out"
}
```

#### POST /api/refresh
Rafraîchir le token JWT

**Headers:**
```
Authorization: Bearer {token}
```

**Response (200):**
```json
{
  "access_token": "new_token_here",
  "token_type": "bearer",
  "expires_in": 3600,
  "user": { ... }
}
```

---

### Routes Admin (auth:api + admin requis)

#### POST /api/register
Créer un nouvel utilisateur

**Headers:**
```
Authorization: Bearer {admin_token}
```

**Body:**
```json
{
  "nom": "Doe",
  "prenom": "John",
  "numero": "771234567",
  "role": "superviseur",
  "email": "john@example.com",
  "password": "password123"
}
```

**Response (201):**
```json
{
  "message": "User created successfully",
  "user": {
    "id": 2,
    "nom": "Doe",
    "prenom": "John",
    "numero": "771234567",
    "role": "superviseur",
    "email": "john@example.com",
    "bloquer": false
  }
}
```

#### PUT /api/users/{id}/block
Bloquer ou débloquer un utilisateur

**Headers:**
```
Authorization: Bearer {admin_token}
```

**Response (200):**
```json
{
  "message": "User blocked successfully",
  "user": {
    "id": 2,
    "nom": "Doe",
    "prenom": "John",
    "bloquer": true
  }
}
```

**Response (403):** Cannot block admin user

#### GET /api/users/non-admin
Lister tous les utilisateurs non admin

**Headers:**
```
Authorization: Bearer {admin_token}
```

**Response (200):**
```json
[
  {
    "id": 2,
    "nom": "Doe",
    "prenom": "John",
    "role": "superviseur",
    "email": "john@example.com",
    "bloquer": false
  }
]
```

---

### Matières Premières

#### POST /api/matieres (Admin)
Créer une matière première

**Headers:**
```
Authorization: Bearer {admin_token}
```

**Body:**
```json
{
  "nom": "Farine",
  "image": "farine.jpg",
  "unite": "kg"
}
```

**Response (201):**
```json
{
  "message": "Matiere premiere created successfully",
  "matiere": {
    "id": 1,
    "nom": "Farine",
    "code": "FAR-AB",
    "image": "farine.jpg",
    "unite": "kg"
  }
}
```

#### PUT /api/matieres/{id} (Admin)
Modifier une matière première

**Headers:**
```
Authorization: Bearer {admin_token}
```

**Body:**
```json
{
  "nom": "Farine",
  "image": "farine.jpg",
  "unite": "kg"
}
```

**Response (200):**
```json
{
  "message": "Matiere premiere updated successfully",
  "matiere": {
    "id": 1,
    "nom": "Farine",
    "code": "FAR-AB",
    "image": "farine.jpg",
    "unite": "kg"
  }
}
```

#### DELETE /api/matieres/{id} (Admin)
Supprimer une matière première

**Headers:**
```
Authorization: Bearer {admin_token}
```

**Response (200):**
```json
{
  "message": "Matiere premiere deleted successfully"
}
```

#### GET /api/matieres
Lister toutes les matières premières

**Headers:**
```
Authorization: Bearer {token}
```

**Response (200):**
```json
[
  {
    "id": 1,
    "nom": "Farine",
    "code": "FAR-AB",
    "image": "farine.jpg",
    "unite": "kg"
  },
  {
    "id": 2,
    "nom": "Sucre",
    "code": "SUC-CD",
    "image": "sucre.jpg",
    "unite": "kg"
  }
]
```

#### GET /api/matieres/{id}
Voir les détails d'une matière première

**Headers:**
```
Authorization: Bearer {token}
```

**Response (200):**
```json
{
  "id": 1,
  "nom": "Farine",
  "code": "FAR-AB",
  "image": "farine.jpg",
  "unite": "kg"
}
```

**Response (404):** Matiere premiere not found

---

### Sites

#### POST /api/sites (Admin)
Créer un site

**Headers:**
```
Authorization: Bearer {admin_token}
```

**Body:**
```json
{
  "nom": "Ferme Principal",
  "adresse": "Dakar, Sénégal",
  "longitude": -17.4440,
  "latitude": 14.7167,
  "longueur": 100.50,
  "largeur": 50.25,
  "gerant": 2
}
```

**Response (201):**
```json
{
  "message": "Site created successfully",
  "site": {
    "id": 1,
    "nom": "Ferme Principal",
    "adresse": "Dakar, Sénégal",
    "longitude": -17.4440,
    "latitude": 14.7167,
    "longueur": 100.50,
    "largeur": 50.25,
    "gerant": 2,
    "gerant_user": {
      "id": 2,
      "nom": "Doe",
      "prenom": "John",
      "email": "john@example.com"
    }
  }
}
```

#### PUT /api/sites/{id} (Admin)
Modifier un site

**Headers:**
```
Authorization: Bearer {admin_token}
```

**Body:**
```json
{
  "nom": "Ferme Principal",
  "adresse": "Dakar, Sénégal",
  "longitude": -17.4440,
  "latitude": 14.7167,
  "longueur": 100.50,
  "largeur": 50.25,
  "gerant": 2
}
```

**Response (200):**
```json
{
  "message": "Site updated successfully",
  "site": {
    "id": 1,
    "nom": "Ferme Principal",
    "adresse": "Dakar, Sénégal",
    "longitude": -17.4440,
    "latitude": 14.7167,
    "longueur": 100.50,
    "largeur": 50.25,
    "gerant": 2,
    "gerant_user": { ... }
  }
}
```

#### DELETE /api/sites/{id} (Admin)
Supprimer un site

**Headers:**
```
Authorization: Bearer {admin_token}
```

**Response (200):**
```json
{
  "message": "Site deleted successfully"
}
```

#### GET /api/sites
Lister tous les sites (avec gérant)

**Headers:**
```
Authorization: Bearer {token}
```

**Response (200):**
```json
[
  {
    "id": 1,
    "nom": "Ferme Principal",
    "adresse": "Dakar, Sénégal",
    "longitude": -17.4440,
    "latitude": 14.7167,
    "longueur": 100.50,
    "largeur": 50.25,
    "gerant": 2,
    "gerant_user": { ... }
  }
]
```

#### GET /api/sites/{id}
Voir les détails d'un site (avec gérant)

**Headers:**
```
Authorization: Bearer {token}
```

**Response (200):**
```json
{
  "id": 1,
  "nom": "Ferme Principal",
  "adresse": "Dakar, Sénégal",
  "longitude": -17.4440,
  "latitude": 14.7167,
  "longueur": 100.50,
  "largeur": 50.25,
  "gerant": 2,
  "gerant_user": { ... }
}
```

**Response (404):** Site not found

---

### Fermes

#### POST /api/fermes (Admin)
Créer une ferme

**Headers:**
```
Authorization: Bearer {admin_token}
```

**Body:**
```json
{
  "nom": "Ferme Nord",
  "idsite": 1,
  "longitude": -17.4500,
  "latitude": 14.7200,
  "longueur": 200.00,
  "largeur": 100.00,
  "gerant": 2
}
```

**Response (201):**
```json
{
  "message": "Ferme created successfully",
  "ferme": {
    "id": 1,
    "nom": "Ferme Nord",
    "idsite": 1,
    "longitude": -17.4500,
    "latitude": 14.7200,
    "longueur": 200.00,
    "largeur": 100.00,
    "gerant": 2,
    "site": { ... },
    "gerant_user": { ... }
  }
}
```

#### PUT /api/fermes/{id} (Admin)
Modifier une ferme

**Headers:**
```
Authorization: Bearer {admin_token}
```

**Body:**
```json
{
  "nom": "Ferme Nord",
  "idsite": 1,
  "longitude": -17.4500,
  "latitude": 14.7200,
  "longueur": 200.00,
  "largeur": 100.00,
  "gerant": 2
}
```

**Response (200):**
```json
{
  "message": "Ferme updated successfully",
  "ferme": {
    "id": 1,
    "nom": "Ferme Nord",
    "idsite": 1,
    "longitude": -17.4500,
    "latitude": 14.7200,
    "longueur": 200.00,
    "largeur": 100.00,
    "gerant": 2,
    "site": { ... },
    "gerant_user": { ... }
  }
}
```

#### DELETE /api/fermes/{id} (Admin)
Supprimer une ferme

**Headers:**
```
Authorization: Bearer {admin_token}
```

**Response (200):**
```json
{
  "message": "Ferme deleted successfully"
}
```

#### GET /api/fermes
Lister toutes les fermes (avec site et gérant)

**Headers:**
```
Authorization: Bearer {token}
```

**Response (200):**
```json
[
  {
    "id": 1,
    "nom": "Ferme Nord",
    "idsite": 1,
    "longitude": -17.4500,
    "latitude": 14.7200,
    "longueur": 200.00,
    "largeur": 100.00,
    "gerant": 2,
    "site": { ... },
    "gerant_user": { ... }
  }
]
```

#### GET /api/fermes/{id}
Voir les détails d'une ferme (avec site et gérant)

**Headers:**
```
Authorization: Bearer {token}
```

**Response (200):**
```json
{
  "id": 1,
  "nom": "Ferme Nord",
  "idsite": 1,
  "longitude": -17.4500,
  "latitude": 14.7200,
  "longueur": 200.00,
  "largeur": 100.00,
  "gerant": 2,
  "site": { ... },
  "gerant_user": { ... }
}
```

**Response (404):** Ferme not found

---

### Magasins

#### POST /api/magasins (Admin)
Créer un magasin

**Headers:**
```
Authorization: Bearer {admin_token}
```

**Body:**
```json
{
  "nom": "Magasin Principal",
  "idsite": 1,
  "longitude": -17.4600,
  "latitude": 14.7300,
  "longueur": 50.00,
  "largeur": 30.00,
  "gerant": 2
}
```

**Response (201):**
```json
{
  "message": "Magasin created successfully",
  "magasin": {
    "id": 1,
    "nom": "Magasin Principal",
    "idsite": 1,
    "longitude": -17.4600,
    "latitude": 14.7300,
    "longueur": 50.00,
    "largeur": 30.00,
    "gerant": 2,
    "site": { ... },
    "gerant_user": { ... }
  }
}
```

#### PUT /api/magasins/{id} (Admin)
Modifier un magasin

**Headers:**
```
Authorization: Bearer {admin_token}
```

**Body:**
```json
{
  "nom": "Magasin Principal",
  "idsite": 1,
  "longitude": -17.4600,
  "latitude": 14.7300,
  "longueur": 50.00,
  "largeur": 30.00,
  "gerant": 2
}
```

**Response (200):**
```json
{
  "message": "Magasin updated successfully",
  "magasin": {
    "id": 1,
    "nom": "Magasin Principal",
    "idsite": 1,
    "longitude": -17.4600,
    "latitude": 14.7300,
    "longueur": 50.00,
    "largeur": 30.00,
    "gerant": 2,
    "site": { ... },
    "gerant_user": { ... }
  }
}
```

#### DELETE /api/magasins/{id} (Admin)
Supprimer un magasin

**Headers:**
```
Authorization: Bearer {admin_token}
```

**Response (200):**
```json
{
  "message": "Magasin deleted successfully"
}
```

#### GET /api/magasins
Lister tous les magasins (avec site et gérant)

**Headers:**
```
Authorization: Bearer {token}
```

**Response (200):**
```json
[
  {
    "id": 1,
    "nom": "Magasin Principal",
    "idsite": 1,
    "longitude": -17.4600,
    "latitude": 14.7300,
    "longueur": 50.00,
    "largeur": 30.00,
    "gerant": 2,
    "site": { ... },
    "gerant_user": { ... }
  }
]
```

#### GET /api/magasins/{id}
Voir les détails d'un magasin (avec site et gérant)

**Headers:**
```
Authorization: Bearer {token}
```

**Response (200):**
```json
{
  "id": 1,
  "nom": "Magasin Principal",
  "idsite": 1,
  "longitude": -17.4600,
  "latitude": 14.7300,
  "longueur": 50.00,
  "largeur": 30.00,
  "gerant": 2,
  "site": { ... },
  "gerant_user": { ... }
}
```

**Response (404):** Magasin not found

---

### Lots

#### POST /api/lots (Admin)
Créer un lot avec plusieurs matières premières

**Headers:**
```
Authorization: Bearer {admin_token}
```

**Body:**
```json
{
  "magasin_id": 1,
  "matieres": [
    {
      "matiere_id": 1,
      "quantite": 50.00
    },
    {
      "matiere_id": 2,
      "quantite": 30.50
    }
  ]
}
```

**Note:** Lors de la création d'un lot, des mouvements de stock de type "entrée" sont automatiquement créés pour chaque matière première avec la quantité spécifiée.

**Response (201):**
```json
{
  "message": "Lot created successfully",
  "lot": {
    "id": 1,
    "code_lot": "050726-01",
    "magasin_id": 1,
    "magasin": {
      "id": 1,
      "nom": "Magasin Principal"
    },
    "matiere_premieres": [
      {
        "id": 1,
        "nom": "Farine",
        "pivot": {
          "lot_id": 1,
          "matiere_premiere_id": 1,
          "quantite": 50.00
        }
      }
    ]
  }
}
```

#### PUT /api/lots/{id} (Admin)
Modifier un lot (matières premières)

**Headers:**
```
Authorization: Bearer {admin_token}
```

**Body:**
```json
{
  "matieres": [
    {
      "matiere_id": 1,
      "quantite": 75.00
    },
    {
      "matiere_id": 2,
      "quantite": 40.00
    }
  ]
}
```

**Response (200):**
```json
{
  "message": "Lot updated successfully",
  "lot": {
    "id": 1,
    "code_lot": "050726-01",
    "matiere_premieres": [
      {
        "id": 1,
        "nom": "Farine",
        "pivot": {
          "lot_id": 1,
          "matiere_premiere_id": 1,
          "quantite": 75.00
        }
      }
    ]
  }
}
```

#### DELETE /api/lots/{id} (Admin)
Supprimer un lot

**Headers:**
```
Authorization: Bearer {admin_token}
```

**Response (200):**
```json
{
  "message": "Lot deleted successfully"
}
```

#### PUT /api/lots/code/{code_lot}/matiere/{code_matiere} (Admin)
Modifier la quantité d'une matière première dans un lot

**Headers:**
```
Authorization: Bearer {admin_token}
```

**Body:**
```json
{
  "quantite": 75.50
}
```

**Response (200):**
```json
{
  "message": "Matiere quantite updated successfully"
}
```

#### DELETE /api/lots/code/{code_lot}/matiere/{code_matiere} (Admin)
Supprimer une matière première d'un lot

**Headers:**
```
Authorization: Bearer {admin_token}
```

**Response (200):**
```json
{
  "message": "Matiere removed from lot successfully"
}
```

#### GET /api/lots
Lister tous les lots (avec matières premières)

**Headers:**
```
Authorization: Bearer {token}
```

**Response (200):**
```json
[
  {
    "id": 1,
    "code_lot": "050726-01",
    "matiere_premieres": [
      {
        "id": 1,
        "nom": "Farine",
        "pivot": {
          "lot_id": 1,
          "matiere_premiere_id": 1,
          "quantite": 100.00
        }
      }
    ]
  }
]
```

#### GET /api/lots/{id}
Voir les détails d'un lot (avec matières premières et statistiques)

**Headers:**
```
Authorization: Bearer {token}
```

**Response (200):**
```json
{
  "id": 1,
  "code_lot": "050726-01",
  "matiere_premieres": [
    {
      "id": 1,
      "nom": "Farine",
      "code": "FAR-AB",
      "unite": "kg",
      "quantite_initiale": 100.00,
      "quantite_sortie": 50.00,
      "quantite_restante": 50.00,
      "gerant": {
        "id": 2,
        "nom": "Doe",
        "prenom": "John",
        "email": "john@example.com"
      },
      "magasin": {
        "id": 1,
        "nom": "Magasin Principal"
      }
    }
  ]
}
```

**Response (404):** Lot not found

#### GET /api/lots/code/{code_lot}
Voir les détails d'un lot par son code (avec matières premières et statistiques)

**Headers:**
```
Authorization: Bearer {token}
```

**Response (200):**
```json
{
  "id": 1,
  "code_lot": "050726-01",
  "matiere_premieres": [
    {
      "id": 1,
      "nom": "Farine",
      "code": "FAR-AB",
      "unite": "kg",
      "quantite_initiale": 100.00,
      "quantite_sortie": 50.00,
      "quantite_restante": 50.00,
      "gerant": {
        "id": 2,
        "nom": "Doe",
        "prenom": "John",
        "email": "john@example.com"
      },
      "magasin": {
        "id": 1,
        "nom": "Magasin Principal"
      }
    }
  ]
}
```

**Response (404):** Lot not found

---

## Formules (Admin et tous les utilisateurs authentifiés)

### GET /api/formules
Lister toutes les formules

**Headers:**
```
Authorization: Bearer {token}
```

**Response (200):**
```json
[
  {
    "id": 1,
    "nom": "Formule Poulet",
    "composant": [
      {
        "matiere_premiere": {
          "id": 1,
          "nom": "Farine",
          "code": "FAR-AB",
          "unite": "kg"
        },
        "quantite": 50.00
      },
      {
        "matiere_premiere": {
          "id": 2,
          "nom": "Maïs",
          "code": "MAI-CD",
          "unite": "kg"
        },
        "quantite": 30.00
      }
    ],
    "created_at": "2026-07-06T23:00:00.000000Z",
    "updated_at": "2026-07-06T23:00:00.000000Z"
  }
]
```

### GET /api/formules/{id}
Voir les détails d'une formule

**Headers:**
```
Authorization: Bearer {token}
```

**Response (200):**
```json
{
  "id": 1,
  "nom": "Formule Poulet",
  "composant": [
    {
      "matiere_premiere": {
        "id": 1,
        "nom": "Farine",
        "code": "FAR-AB",
        "unite": "kg"
      },
      "quantite": 50.00
    }
  ],
  "created_at": "2026-07-06T23:00:00.000000Z",
  "updated_at": "2026-07-06T23:00:00.000000Z"
}
```

**Response (404):** Formule not found

### POST /api/formules
Créer une formule

**Headers:**
```
Authorization: Bearer {admin_token}
```

**Body:**
```json
{
  "nom": "Formule Poulet",
  "composant": [
    {
      "matiere_id": 1,
      "quantite": 50.00
    },
    {
      "matiere_id": 2,
      "quantite": 30.00
    }
  ]
}
```

**Response (201):**
```json
{
  "message": "Formule created successfully",
  "formule": {
    "id": 1,
    "nom": "Formule Poulet",
    "composant": [
      {
        "matiere_id": 1,
        "quantite": 50.00
      }
    ],
    "created_at": "2026-07-06T23:00:00.000000Z",
    "updated_at": "2026-07-06T23:00:00.000000Z"
  }
}
```

### PUT /api/formules/{id}
Modifier une formule

**Headers:**
```
Authorization: Bearer {admin_token}
```

**Body:**
```json
{
  "nom": "Formule Poulet Modifiée",
  "composant": [
    {
      "matiere_id": 1,
      "quantite": 60.00
    }
  ]
}
```

**Response (200):**
```json
{
  "message": "Formule updated successfully",
  "formule": {
    "id": 1,
    "nom": "Formule Poulet Modifiée",
    "composant": [
      {
        "matiere_id": 1,
        "quantite": 60.00
      }
    ],
    "created_at": "2026-07-06T23:00:00.000000Z",
    "updated_at": "2026-07-06T23:30:00.000000Z"
  }
}
```

**Response (404):** Formule not found

### DELETE /api/formules/{id}
Supprimer une formule

**Headers:**
```
Authorization: Bearer {admin_token}
```

**Response (200):**
```json
{
  "message": "Formule deleted successfully"
}
```

**Response (404):** Formule not found

---

## Aliments (Admin et tous les utilisateurs authentifiés)

### GET /api/aliments
Lister tous les aliments

**Headers:**
```
Authorization: Bearer {token}
```

**Response (200):**
```json
[
  {
    "id": 1,
    "nom": "Poulet",
    "code": "al-1234",
    "created_at": "2026-07-06T23:00:00.000000Z",
    "updated_at": "2026-07-06T23:00:00.000000Z"
  }
]
```

### GET /api/aliments/{id}
Voir les détails d'un aliment

**Headers:**
```
Authorization: Bearer {token}
```

**Response (200):**
```json
{
  "id": 1,
  "nom": "Poulet",
  "code": "al-1234",
  "created_at": "2026-07-06T23:00:00.000000Z",
  "updated_at": "2026-07-06T23:00:00.000000Z"
}
```

**Response (404):** Aliment not found

### POST /api/aliments
Créer un aliment

**Headers:**
```
Authorization: Bearer {admin_token}
```

**Body:**
```json
{
  "nom": "Poulet"
}
```

**Note:** Le code est généré automatiquement au format al-XXXX.

**Response (201):**
```json
{
  "message": "Aliment created successfully",
  "aliment": {
    "id": 1,
    "nom": "Poulet",
    "code": "al-1234",
    "created_at": "2026-07-06T23:00:00.000000Z",
    "updated_at": "2026-07-06T23:00:00.000000Z"
  }
}
```

### PUT /api/aliments/{id}
Modifier un aliment

**Headers:**
```
Authorization: Bearer {admin_token}
```

**Body:**
```json
{
  "nom": "Poulet Bio"
}
```

**Response (200):**
```json
{
  "message": "Aliment updated successfully",
  "aliment": {
    "id": 1,
    "nom": "Poulet Bio",
    "code": "al-1234",
    "created_at": "2026-07-06T23:00:00.000000Z",
    "updated_at": "2026-07-06T23:30:00.000000Z"
  }
}
```

**Response (404):** Aliment not found

### DELETE /api/aliments/{id}
Supprimer un aliment

**Headers:**
```
Authorization: Bearer {admin_token}
```

**Response (200):**
```json
{
  "message": "Aliment deleted successfully"
}
```

**Response (404):** Aliment not found

---

### Mouvements de Stock (Admin et Comptable)

#### POST /api/mouvement-stocks
Créer un mouvement de stock

**Headers:**
```
Authorization: Bearer {admin_or_comptable_token}
```

**Body:**
```json
{
  "magasin_id": 1,
  "matiere_id": 1,
  "lot_id": 1,
  "type": "entree",
  "quantite": 100.50,
  "date_mouvement": "2026-07-05",
  "observation": "Entrée de stock"
}
```

**Note:** Le champ `gerant_id` est automatiquement défini avec l'utilisateur connecté.

**Note:** Pour les sorties (`type: "sortie"`), le système vérifie que la quantité demandée ne dépasse pas le stock disponible. Si le stock est insuffisant, une erreur 400 est retournée avec le stock disponible.

**Response (400) - Stock insuffisant:**
```json
{
  "error": "Quantité insuffisante",
  "stock_disponible": 50.00,
  "quantite_demandee": 100.00
}
```

**Response (201):**
```json
{
  "message": "Mouvement stock created successfully",
  "mouvement": {
    "id": 1,
    "magasin_id": 1,
    "matiere_id": 1,
    "lot_id": 1,
    "type": "entree",
    "quantite": 100.50,
    "date_mouvement": "2026-07-05",
    "gerant_id": 2,
    "observation": "Entrée de stock",
    "magasin": { ... },
    "matiere_premiere": { ... },
    "lot": { ... },
    "gerant": { ... }
  }
}
```

#### PUT /api/mouvement-stocks/{id}
Modifier un mouvement de stock

**Headers:**
```
Authorization: Bearer {admin_or_comptable_token}
```

**Body:**
```json
{
  "magasin_id": 1,
  "matiere_id": 1,
  "lot_id": 1,
  "type": "sortie",
  "quantite": 50.00,
  "date_mouvement": "2026-07-05",
  "observation": "Sortie modifiée"
}
```

**Note:** Le champ `gerant_id` est automatiquement défini avec l'utilisateur connecté.

**Response (200):**
```json
{
  "message": "Mouvement stock updated successfully",
  "mouvement": {
    "id": 1,
    "magasin_id": 1,
    "matiere_id": 1,
    "lot_id": 1,
    "type": "sortie",
    "quantite": 50.00,
    "date_mouvement": "2026-07-05",
    "gerant_id": 2,
    "observation": "Sortie modifiée",
    "magasin": { ... },
    "matiere_premiere": { ... },
    "lot": { ... },
    "gerant": { ... }
  }
}
```

**Response (400):** Quantité insuffisante (si sortie)

#### DELETE /api/mouvement-stocks/{id}
Supprimer un mouvement de stock

**Headers:**
```
Authorization: Bearer {admin_or_comptable_token}
```

**Response (200):**
```json
{
  "message": "Mouvement stock deleted successfully"
}
```

#### GET /api/mouvement-stocks
Lister tous les mouvements de stock

**Headers:**
```
Authorization: Bearer {admin_or_comptable_token}
```

**Response (200):**
```json
[
  {
    "id": 1,
    "magasin_id": 1,
    "matiere_id": 1,
    "lot_id": 1,
    "type": "entree",
    "quantite": 100.00,
    "date_mouvement": "2026-07-05",
    "gerant_id": 2,
    "observation": "Entrée de stock",
    "magasin": { ... },
    "matiere_premiere": { ... },
    "lot": { ... },
    "gerant": { ... }
  }
]
```

#### GET /api/mouvement-stocks/{id}
Voir les détails d'un mouvement de stock

**Headers:**
```
Authorization: Bearer {admin_or_comptable_token}
```

**Response (200):**
```json
{
  "id": 1,
  "magasin_id": 1,
  "matiere_id": 1,
  "lot_id": 1,
  "type": "entree",
  "quantite": 100.00,
  "date_mouvement": "2026-07-05",
  "gerant_id": 2,
  "observation": "Entrée de stock",
  "magasin": { ... },
  "matiere_premiere": { ... },
  "lot": { ... },
  "gerant": { ... }
}
```

**Response (404):** Mouvement stock not found

#### GET /api/mouvement-stocks/lot/{code_lot}
Lister tous les mouvements d'un lot pour chaque matière première

**Headers:**
```
Authorization: Bearer {admin_or_comptable_token}
```

**Response (200):**
```json
{
  "lot": {
    "id": 1,
    "code_lot": "050726-01",
    "matiere_premieres": [...]
  },
  "mouvements": [
    {
      "id": 1,
      "type": "entree",
      "quantite": 100.00,
      "date_mouvement": "2026-07-05",
      "magasin": {...},
      "matiere_premiere": {...},
      "gerant": {...}
    }
  ]
}
```

**Response (404):** Lot not found

#### GET /api/mouvement-stocks/lot/{code_lot}/statistics
Statistiques d'un lot pour chaque matière première

**Headers:**
```
Authorization: Bearer {admin_or_comptable_token}
```

**Response (200):**
```json
{
  "lot": {
    "id": 1,
    "code_lot": "050726-01"
  },
  "statistics": [
    {
      "matiere_premiere": {
        "id": 1,
        "nom": "Farine",
        "code": "FAR-AB"
      },
      "quantite_initiale": 100.00,
      "somme_entree": 500.00,
      "somme_sortie": 200.00,
      "difference_mouvements": 300.00,
      "stock_actuel": 400.00
    }
  ]
}
```

**Response (404):** Lot not found

#### GET /api/mouvement-stocks/magasin/{magasin_id}
Lister tous les mouvements d'un magasin

**Headers:**
```
Authorization: Bearer {admin_or_comptable_token}
```

**Response (200):**
```json
[
  {
    "id": 1,
    "magasin_id": 1,
    "matiere_id": 1,
    "lot_id": 1,
    "type": "entree",
    "quantite": 100.00,
    "date_mouvement": "2026-07-05",
    "gerant_id": 2,
    "observation": "Entrée de stock",
    "matiere_premiere": { ... },
    "lot": { ... },
    "gerant": { ... }
  }
]
```

#### GET /api/mouvement-stocks/magasin/{magasin_id}/statistics
Statistiques d'un magasin pour chaque matière première

**Headers:**
```
Authorization: Bearer {admin_or_comptable_token}
```

**Response (200):**
```json
[
  {
    "matiere_premiere": {
      "id": 1,
      "nom": "Farine",
      "code": "FAR-AB"
    },
    "somme_entree": 500.00,
    "somme_sortie": 200.00,
    "difference": 300.00,
    "stock_actuel": 300.00
  }
]
```

---

## Rôles et Permissions

### Admin
- Créer des utilisateurs
- Bloquer/débloquer des utilisateurs (sauf admin)
- Créer, modifier, supprimer des matières premières
- Créer, modifier, supprimer des sites
- Créer, modifier, supprimer des fermes
- Créer, modifier, supprimer des magasins
- Créer, modifier, supprimer des lots
- Accès en lecture à toutes les ressources

### Superviseur
- Accès en lecture à toutes les ressources

### Comptable
- Accès en lecture à toutes les ressources
- Créer, modifier, supprimer des mouvements de stock
- Accéder aux statistiques de lots et magasins

---

## Sécurité

### Middleware
- **auth:api**: Vérifie que l'utilisateur est authentifié via JWT
- **admin**: Vérifie que l'utilisateur a le rôle 'admin'
- **adminOrComptable**: Vérifie que l'utilisateur a le rôle 'admin' ou 'comptable'

### Blocage des utilisateurs
- Si un utilisateur a `bloquer = true`, la connexion est refusée
- Les admins ne peuvent pas être bloqués
- Le blocage est géré via l'API `/api/users/{id}/block`

### Cascade Delete
- Lorsqu'un site est supprimé, toutes les fermes et magasins associés sont supprimés
- Lorsqu'un utilisateur est supprimé, tous les sites/fermes/magasins où il est gérant sont supprimés
- Lorsqu'une matière première est supprimée, tous les lots et mouvements associés sont supprimés
- Lorsqu'un lot est supprimé, tous les mouvements associés sont supprimés

---

## Installation

### Prérequis
- PHP 8.2+
- MySQL
- Composer

### Étapes
1. Cloner le projet
2. Configurer le fichier `.env` avec les informations de la base de données
3. Installer les dépendances: `composer install`
4. Générer la clé JWT: `php artisan jwt:secret`
5. Exécuter les migrations: `php artisan migrate`
6. Démarrer le serveur: `php artisan serve`

---

## Notes importantes

- Le code des matières premières est généré automatiquement
- Tous les champs `gerant` sont des clés étrangères vers la table `users`
- Les mots de passe ne sont jamais retournés dans les réponses API
- Les réponses incluent les relations (site, gérant) pour faciliter l'utilisation
