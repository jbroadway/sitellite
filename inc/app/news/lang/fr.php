<?php

// BEGIN KEEPOUT CHECKING
// Add these lines to the very top of any file you don't want people to
// be able to access directly.
if (! defined ('SAF_VERSION')) {
  header ('HTTP/1.1 404 Not Found');
  echo "<!DOCTYPE HTML PUBLIC \"-//IETF//DTD HTML 2.0//EN\">\n"
      . "<html><head>
<title>404 Not Found</title>
</head><body>
<h1>Not Found</h1>\n"
      . "The requested URL " . $PHP_SELF . " was not found on this server.<p>\n<hr>\n"
      . $_SERVER['SERVER_SIGNATURE'] . "</body></html>";
  exit;
}
// END KEEPOUT CHECKING

$this->lang_hash['fr'] = array (
	'Access Level' => 'Niveau d\'accès',
	'Add Comment' => 'Ajouter un commentaire',
	'Adding News Story' => 'Ajouter une nouvelle',
	'Alias of a page' => 'Alias d\'une page',
	'Align thumbnails' => 'Aligner miniatures',
	'All Authors' => 'Tous les auteurs',
	'All Sections' => 'Toutes les rubriques',
	'Allow comments' => 'Autoriser les commentaires',
	'Approved' => 'Approuvé',
	'Archive On (If Status is &quot;Approved&quot;)' => 'Archiver le (si ce statut est «approuvé»)',
	'Author' => 'Auteur',
	'Authors' => 'Auteurs',
	'Back' => 'Retour',
	'Back to Comments' => 'Retour aux commentaires',
	'Blog-style comment display' => 'Commentaires style «blog»',
	'Body (XHTML format, use <hr /> to separate multiple pages)' => 'Corps (format XHTML, utilisez <hr /> pour séparer plusieurs pages)',
	'By' => 'Par',
	'Cancel' => 'Annuler',
	'Category' => 'Catégorie',
	'Change Summary' => 'Résumé des modifications',
	'Comment (no HTML)' => 'Commentaire (pas de HTML)',
	'Comment Added' => 'Commentaire ajouté',
	'Comment Deleted' => 'Commentaire supprimé',
	'Comment Updated' => 'Commentaire mis à jour',
	'Comments' => 'Commentaires',
	'Create' => 'Créer',
	'Created By' => 'Créé par',
	'Delete Comment' => 'Supprimer le commentaire',
	'Edit' => 'Modifier',
	'Edit Comment' => 'Modifier le commentaire',
	'Editing Comment' => 'Modification du commentaire',
	'Editing News Story' => 'Modification de la nouvelle',
	'Forward to (URL)' => 'Rediriger vers (URL)',
	'Home' => 'Accueil',
	'Item Locked by Another User' => 'Élément vérouillé par un autre utilisateur',
	'Latest Articles' => 'Derniers articles',
	'Main page title' => 'Titre de la page principale',
	'More News' => 'Plus de nouvelles',
	'Name' => 'Nom',
	'News' => 'Nouvelles',
	'News Comment Notice' => 'Avis de commentaire de nouvelles',
	'News Search' => 'Recherche de nouvelles',
	'News Stories' => 'Articles',
	'News from' => 'Nouvelle de',
	'Next' => 'Suivant',
	'No comments.' => 'Aucun commentaire.',
	'No stories found.' => 'Aucune histoire trouvée.',
	'No stories found.  Please try again.' => 'Aucune histoire trouvée. Veuillez essayez de nouveau.',
	'Number of stories on front page' => 'Nombre d\'articles sur la première page',
	'Number of stories per page' => 'Nombre d\'articles par page',
	'Owned by Team' => 'Appartient à l\'équipe',
	'Page' => 'Page',
	'Page Title' => 'Titre de la page',
	'Pending' => 'En attente',
	'Please use this form to submit stories for possible inclusion on our site.' => 'Veuillez utiliser ce formulaire pour soumettre des articles pour une éventuelle inclusion sur notre site.',
	'Post' => 'Envoyer',
	'Previous' => 'Précédent',
	'Properties' => 'Propriétés',
	'Publish On (If Status is &quot;Queued&quot;)' => 'Publier le (si ce statut est «en attente»)',
	'Rank' => 'Classement',
	'Rejected' => 'Rejeté',
	'Save' => 'Enregistrer',
	'Save and continue' => 'Enregistrer et continuer',
	'Search' => 'Rechercher',
	'Section' => 'Rubrique',
	'Sections' => 'Rubriques',
	'Send comments to (email)' => 'Envoyez les commentaires à (courriel)',
	'State' => 'État',
	'Status' => 'État',
	'Story Submissions' => 'Propositions d\'articles',
	'Story not found.' => 'Article non-trouvé.',
	'Story submissions (email, optional)' => 'Propositions d\'articles (courriel, facultatif)',
	'Subject' => 'Sujet',
	'Submissions' => 'Soumissions',
	'Submit A Story' => 'Soumettre un article',
	'Subscribe (RSS)' => 'S\'abonner (RSS)',
	'Summary' => 'Résumé',
	'Summary Thumbnail' => 'Miniature de résumé',
	'Template' => 'Modèle',
	'Thank You!' => 'Merci!',
	'Title' => 'Titre',
	'Use CAPTCHA on comments' => 'Utilisez CAPTCHA pour les commentaires',
	'Use sections' => 'Utilisez les rubriques',
	'You must enter a body.' => 'Vous devez entrer un corps.',
	'You must enter a comment body.' => 'Vous devez entrer un corps de commentaire.',
	'You must enter a name.' => 'Vous devez entrer un nom.',
	'You must enter a subject line.' => 'Vous devez entrer une ligne de sujet.',
	'You must enter a summary.' => 'Vous devez entrer un résumé.',
	'You must enter a title for your news story.' => 'Vous devez entrer un titre pour votre nouvelles.',
	'You must enter a title.' => 'Vous devez entrer un titre.',
	'You must enter an author name.' => 'Vous devez entrer un nom d\'auteur.',
	'You must enter content into your news story.' => 'Vous devez entrer un contenu pour votre nouvelle.',
	'Your comment has been added.' => 'Votre commentaire a été ajouté.',
	'Your comment has been deleted.' => 'Votre commentaire a été supprimé.',
	'Your comment has been updated.' => 'Votre commentaire a été mis à jour.',
	'Your item has been created.' => 'Votre élément a été créé.',
	'Your item has been saved.' => 'Vos éléments ont été enregistrés.',
	'Your submission has been received.  It will be reviewed shortly by the web site editors.' => 'Votre proposition d\'article a été reçue. Elle sera examinée prochainement par les éditeurs du site web.',
	'news' => 'nouvelles',
	'stories by' => 'article de',
	'Security Test' => 'Test de sécurité',
);

?>