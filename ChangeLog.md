# CHANGELOG MODULE PVPROPAL FOR [DOLIBARR ERP CRM](https://www.dolibarr.org)

## 1.0

- Initial version
- Ajout du dictionnaire des spécifications de panneaux photovoltaïques
- Préremplissage du dictionnaire PV avec les spécifications standards
- Ajout des colonnes Type de caractéristiques et Position pour le dictionnaire PV
- Sécurisation de la mise à niveau du dictionnaire PV en ajoutant les colonnes et valeurs manquantes lors des activations.
- Compatibilité de la mise à niveau du dictionnaire PV sans dépendre de DDLFieldExists.
- Ajout du préremplissage des caractéristiques thermiques et de conditionnement dans le dictionnaire PV.
- Ajout automatique de la nature de produit Module Photovoltaïque lors de l'activation du module.
- Sécurisation de la création de la nature de produit pour les dictionnaires utilisant des codes numériques.
