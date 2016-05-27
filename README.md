revizmaths
==========
# tests PHPUnit / Travis

[![Build Status](https://travis-ci.org/Antoine07/revizmaths.svg?branch=dev)](https://github.com/Antoine07/revizmaths)


# relation Doctrine unidirectionnelle

commandes pour mettre à jour Doctrine et la base de données

``` bash
// génération des setter et getter
$ php bin/console doctrine:generate:entities App\AppBundle

// mise à jour de la base de données
$ php bin/console doctrine:schema:update --force

```

## OneToOne

- relation unique entre deux entités
- un avatar est lié  à un unique utilisateur et un utilisateur ne possède qu'un avatar
- la relation User est dite propriétaire (possède image_id FK)

``` php

class User
{
    /**
     * @ORM\OneToOne(targetEntity="AppBundle\Entity\Image", cascade={"persist"})
    */
    private $image;
}

class Image {// rien ici ...}

```
- @ORM\OneToOne incompatible avec \@ORM\Column
- targetEntity namespace de l'entité en relation
- cascade (facultative) persist entité associée persistée, remove entité associée supprimée

``` php

$user = new User;
$user->setUsername('Tony');
...
$image = new Image;
$image->setUrl('image.jpg');
...

$user->setImage($image); // cascade persite
// sans l'option persist il faudrait faire pour faire persister l'entité Image:
// $em->persist($image);
$em->persist($user);
$em->flush();

$em->remove($user); // cascade remove => suppression de l'entité Image associée !

```
- par défaut une relation est facultative, un utilisateur sans avatar est donc possible, ici
- @ORM\JoinColumn(nullable=false) précise si la relation doit être facultative ou non

``` php

class User
{
    /**
     * @ORM\OneToOne(targetEntity="AppBundle\Entity\Image", cascade={"persist"})
     * @ORM\JoinColumn(nullable=false)
    */
    private $image;
}

```

##  relation ManyToOne

- une entité peut avoir des relations avec plusieurs entitées

``` php

class Post
{
    /**
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Category", cascade={"persist", "remove"})
    */
    private $category;
}

class Category {// rien ici ...}

// récupérer tous les articles d'une catégorie sans la relation bidirectionnelle, à l'aide du Repository
 $posts = $em
            ->getRepository('RevizFrontBundle:Post')
            ->findBy(['category' => 1]);

// la relation de bidirectionnalité permet d'optimiser cette requête

```

- @ORM\JoinColumn(nullable=false) false => un article post ne pas être associé à une catégorie

##  relation ManyToMany

- les entitées sont en relation de manière réciproque, comme avec les tags et les posts
- la table de liaison lors de la génération est créé automatiquement

``` php

class Post
{
    /**
     * @ORM\ManyToMany(targetEntity="AppBundle\Entity\Tag", cascade={"persist"})
    */
    private $tags;
}

class Tag {// rien ici ...}

```

# relation Doctrine bidirectionnelle

Dans ce cas il faut modifier la relation inverse à la relation propriétaire

``` php

class Post
{
   /**
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Category", cascade={"persist"}, inversedBy="posts")
     * @ORM\JoinColumn(nullable=false)
    */
    private $category;
}

class Category {

    /**
     * @ORM\OneToMany(targetEntity="AppBundle\Entity\Post", mappedBy="category")
    */
    private $posts;

    ...

    public function addPost(Post $post)
      {
        $this->$posts[] = $post;

        // On lie le post à la catégorie automatiquement
        $post->setCategory($this);

        return $this;
      }

}

// exemple
$post = new Post;
...
$category = new Category;

$category->addPost($post);
// ce n'est pas la peine de faire cela car la relation est déjà liée automatiquement voir ci-dessus addPost
// $post->setCategory($category);

```

## Les services

- Un service est une classe PHP, il a pour vocation d'être accessible de partout dans le code.
- On crée un service par fonctionnalité.
- orienté son code service permet de séparé son code en single responsability

- Symfony possède un conteneur de services.

- définir un service:
* son nom
* la classe 
* les arguments: un autre service ou de(s) paramètre(s) (parameters.yml)
* un service est partagé ie si une instance d'un service est faite et que plus tard dans le script on réutilise sce service, 
c'est la même instance de ce service qui est utilisé
* tag de service pour récupérer tous les services ayant le même tag
* pour définir un tag 


``` bash
// listes des services public
$ php bin/console debug:container

// services privés du framework
$ php bin/console debug:container --show-private

// information sur un service en particulier
$ php bin/console debug:container reviz.antispam

```

Toutes les classes du framework dérivant de la classe ContainerAware ont accès aux services de Symfony

``` php
...
class DefaultController extends Controller
{

    public function indexAction()
    {
        $mailer = $this->container->get('mailer');
    }
}

```

### création d'un service

``` php
namespace Reviz\FrontBundle\Utils;


class AntiSpam
{

    private $word;
    private $parameters;

    public function __construct(Regex $regex, $foo, $bar)
    {
        $this->word = $regex->get('hello');
        $this->parameters[] = $foo;
        $this->parameters[] = $bar;
    }

    public function isSpam($text)
    {
        return preg_match("/{$this->word}/", $text);
    }
}

```

- configuration d'un service

Dans le fichier app/config/service.yml (global)

``` yaml

parameters:
    parameter_name: je suis un paramètre sympa

services:
    reviz.regex:
        class: Reviz\FrontBundle\Utils\Regex

    reviz.antispam:
        class: Reviz\FrontBundle\Utils\AntiSpam
        arguments: ["@reviz.regex", "c'est foo", "%parameter_name%"]

```

# Event Listener

Définir l'événement dans le dossier config des services, un service est une classe, avec une méthode one+"camel-cased event name"
par exemple si on se branche sur le listener kernel.exception => onKernelException()

``` bash
services:
    reviz.exception:
        class: Reviz\FrontBundle\Utils\Event\RevizException
        tags:
            - { name: kernel.event_listener, event: kernel.exception }

```

debug event listener

``` bash
$ php bin/console debug:event-dispatcher
$ php bin/console debug:event-dispatcher kernel.exception

```

Une classe dans son Bundle pour gérer les exceptions de l'application:

``` php
namespace Reviz\FrontBundle\Utils\Event;

use Symfony\Component\HttpKernel\Event\GetResponseForExceptionEvent;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;

class RevizException
{
    public function onKernelException(GetResponseForExceptionEvent $event)
    {
        // You get the exception object from the received event
        $exception = $event->getException();
        $message = sprintf(
            'My Error says: %s with code: %s',
            $exception->getMessage(),
            $exception->getCode()
        );

        // Customize your response object to display the exception details
        $response = new Response();
        $response->setContent($message);

        // HttpExceptionInterface is a special type of exception that
        // holds status code and header details
        if ($exception instanceof HttpExceptionInterface) {
            $response->setStatusCode($exception->getStatusCode());
            $response->headers->replace($exception->getHeaders());
        } else {
            $response->setStatusCode(Response::HTTP_INTERNAL_SERVER_ERROR);
        }

        // Send the modified response object to the event
        $event->setResponse($response);
    }
}
```

# créer un formulaire

``` php

// dans une méthode de contrôleur

$form = $this->createFormBuilder()
             ->add('name', TextType::class)
             ->getForm();

// injection dans twig

return $this->render('front/login.html.twig',[
                     'form' => $form->createView()
]);

```

## utilisé une entité 

``` php


public function newAction(Request $request)
    {
        // create a method and give it some dummy data
        $method = new Method(); // entity Method
        $method->setTitle('Suite récurente');
        $method->setCreatedAt(new \DateTime('tomorrow'));

        $form = $this->createFormBuilder($method)
            ->add('title', TextType::class)
            ->add('createdAt', DateType::class)
            ->add('save', SubmitType::class, ['label'=>'create method'])
            ->getForm();

        return $this->render('default/new.html.twig', array(
            'form' => $form->createView(),
        ));
    }

```

## utilisé le formbuilder 

# authentification 
 - deux notions authentification firewall et autorisation agit après le firewall
 
 - processus
 * un utilisateur veut accéder à une ressource protégée
 * firewall redirige l'utilisateur vers le formulaire de connexion
 * l'utilisateur s'authentifie
 * le firewall authentifie l'utilisateur
 * l'utilisateur est renvoyé à la requête initiale
 * le contrôle d'accès vérifie les droits utilisateurs => autorise ou non l'accès
 
 remarque l'authentification peut se faire à l'aide auth facebook, google...
 
 - la configuration de la sécurité dépend d'un Bundle SecurityBundle
 dans le fichier security.yml de app/config/security.yml
 
 ``` yaml
 # To get started with security, check out the documentation:
 # http://symfony.com/doc/current/book/security.html
 security:
     encoders:
        # encodage du mot de passe plaintext => en clair pour les tests, utilisé un sha1
        Symfony\Component\Security\Core\User\User: plaintext
        # encodage bcrypt et modèle User
        # Symfony\Component\Security\Core\User\User:
        #    algorithm: bcrypt
        #    cost: 12
     # assignation des roles, utilisateur doit avoir un ou des roles => accès 
     # non des roles ROLE_...
     role_hierarchy:
        # l'admin possède le role ROLE_USER => il pourra accéder au page disposant de ce role
         ROLE_ADMIN:       ROLE_USER
         ROLE_SUPER_ADMIN: [ROLE_USER, ROLE_ADMIN, ROLE_ALLOWED_TO_SWITCH]
     # http://symfony.com/doc/current/book/security.html#where-do-users-come-from-user-providers
     providers:
        # pour les tests de dev in_memory les utilisateurs sont définis dans ce fichier à changer en prod
         in_memory:
             memory:
               users:
                 ryan:
                   password: ryanpass
                   roles: 'ROLE_USER'
                 admin:
                   password: admin
                   roles: 'ROLE_ADMIN'
 
     firewalls:
         firewalls:
            dev:
              pattern: ^/(_(profiler|wdt)|css|images|js)/
              security: false
           # derrière le parefeu tout le site
            main:
              pattern:   ^/
              # les anonymous sont authentifiés, si false aucun accès
              anonymous: true
              provider:       in_memory
                 form_login:
                     login_path: login
                     check_path: login_check
                 logout:
                     path:       logout
                     target:     /platform
 
             # http_basic: ~
             # http://symfony.com/doc/current/book/security.html#a-configuring-how-your-users-will-authenticate
 
             # form_login: ~
             # http://symfony.com/doc/current/cookbook/security/form_login_setup.html
             
    # active la securité sur les routes directement, possibilité dans les controllers
    access_control:
        #- { path: ^/login, roles: IS_AUTHENTICATED_ANONYMOUSLY, requires_channel: https }
 ``` 

- il faut créer un Bundle spécifique pour la sécurité dans Symfony, c'est une meilleur pratique


# elasticsearch

- créer un contenu index ~ db type ~table index/type

``` json
POST /reviz/videos
{
    "title" : "recherche des nombres premiers",
    "module" : "Arithmétique",
    "formule": "Terminale",
    "tags": ["terminale", "arithémtique", "methode"],
    "duration": 600,
    "autorisation": {"ref": "123", "name": ["antoine.lucsko@w.fr", "simon@simon.fr","antoine.m@g.fr" ]}
    "description": "blablabla",
    "link": "/video/1253"
}

```

- pour afficher/supprimer un résultat, on récupère l'identifiant de la resource AVTYQ-GedRGx4EqfVSwr

``` json

GET /reviz/videos/AVTYQ-GedRGx4EqfVSwr
DELETE /reviz/videos/AVTYQ-GedRGx4EqfVSwr

```

- pour visualiser un retour plus explicite de la requête, administration type pgadmin ou phpadmin
attention \ sous windows
elasticsearch/bin/plugin install mobz/elasticsearch-head

- une fois elasticsearch lancé on y accède à l'adresse suivante:

http://127.0.0.1:9200/_plugin/head/

- faire une recherche 

query DSL

``` json
# retourne toutes les données hits les données retournées

POST /reviz/videos/_search
 {
     "query": {
         "match_all": {
             
         }
     }
 }
 
 POST /reviz/videos/_search
 {
     "query": {
         "match": {
            "title" : "arithmétique"
             
         }
     }
 }
 
 # faire une recherche que sur un champ
 
  POST /reviz/videos/_search
  {
      "query": {
          "query_string": {
             "query" : "arithmétique",
             "fields": ["module"]
              
          }
      }
  }
  
# query permet de faire des requêtes avec des commandes

POST /reviz/videos/_search
{
  "query": {
      "query_string": {
         "query" : "module:arithmétique title:nombres"       
      }
  }
}

``` 

- la méthode PUT permet de modifier un enregistrement 

``` json

PUT /reviz/videos/AVTYQ-GedRGx4EqfVSwr
{
    "title" : "recherche des nombres premiers",
    "module" : "Arithmétique",
    "formule": "Terminale",
    "tags": ["terminale", "arithémtique", "methode"],
    "duration": 600,
    "autorisation": {"ref": "123", "name": ["antoine.lucsko@w.fr", "simon@simon.fr","antoine.m@g.fr" ]}
    "description": "blablabla",
    "link": "/video/1253"
}

```

- fuzziness

nombre~  => trouvera nombres


- exemple de recherche plus poussée avec des ranges

- option match ou match_phrase doit matcher dans l'ordre des mots
- range sur un champ gte ou lte

``` json

  POST /reviz/videos/_search
  {
  "query":{
    "filtered": {
      "query": {
        "match_phrase": { "title": "nombres premier" }
      },
      "filter": {
        "range": { "duration": { "gte": 60 }}
      }
    }
  }
  
  }

```




