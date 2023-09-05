<?php

namespace App\DataFixtures;

use DateTime;
use App\Entity\Project;
use App\Entity\SchoolYear;
use App\Entity\Student;
use App\Entity\Tag;
use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Bundle\FixturesBundle\FixtureGroupInterface;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory as FakerFactory;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class TestFixtures extends Fixture implements FixtureGroupInterface
{
    private $faker;
    private $hasher;
    private $manager;

    public function __construct(UserPasswordHasherInterface $hasher)
    {
        $this->faker = FakerFactory::create('fr_FR');
        $this->hasher = $hasher;
    }

    public static function getGroups(): array
    {
        return ['test'];
    }

    public function load(ObjectManager $manager): void
    {
        $this->manager = $manager;

        $this->loadTags();
        $this->loadSchoolYears();
        $this->loadProjects();
        $this->loadStudent();
    }

    public function loadTags(): void
    {
        //données statiques
        $datas = [
            [
                'name' => 'HTML',
                'description' => null,
            ],
            [
                'name' => 'CSS',
                'description' => null,
            ],
            [
                'name' => 'JS',
                'description' => null,
            ],
        ];

        foreach ($datas as $data) {
            $tag = new Tag();
            $tag->setName($data['name']);
            $tag->setDescription($data['description']);

            $this->manager->persist($tag);
        }

        //données dynamiques
        for ($i = 0; $i < 10; $i++) {
            $tag = new Tag();
            $words = random_int(1, 3);
            $tag->SetName($this->faker->unique()->sentence($words));
            $words = random_int(8, 15);
            $tag->setDescription($this->faker->sentence($words));

            $this->manager->persist($tag);
        }


        $this->manager->flush();
    }


    public function loadSchoolYears(): void
    {
        //données static
        $datas = [
            [
                'name' => 'Alan Turing',
                'description' => null,
                'startDate' => new DateTime('2022-01-01'),
                'endDate' => new DateTime('2022-12-31'),
            ],
            [
                'name' => 'John van Neuman',
                'description' => null,
                'startDate' => new DateTime('2022-06-01'),
                'endDate' => new DateTime('2023-05-31'),
            ],
            [
                'name' => 'Brendan Eich',
                'description' => null,
                'startDate' => null,
                'endDate' => null,
            ],
        ];

        foreach ($datas as $data) {
            $schoolYear = new SchoolYear();
            $schoolYear->setName($data['name']);
            $schoolYear->setDescription($data['description']);
            $schoolYear->setStartDate($data['startDate']);
            $schoolYear->setEndDate($data['endDate']);

            $this->manager->persist($schoolYear);
        }

        $this->manager->flush();

        //données dynamiques
        for ($i = 0; $i < 10; $i++) {
            $schoolYear = new SchoolYear();
            $words = random_int(2, 4);
            $schoolYear->SetName($this->faker->unique()->sentence($words));
            $words = random_int(8, 15);
            $schoolYear->setDescription($this->faker->optional($weight = 0.7)->sentence($words));
            $startDate = $this->faker->dateTimeBetween('-1 year', '-6 months');
            $schoolYear->setStartDate($startDate);
            $endDate = $this->faker->dateTimeBetween('-6 months', 'now');
            $schoolYear->setEndDate($endDate);

            $this->manager->persist($schoolYear);
        }

        $this->manager->flush();
    }

    public function loadProjects(): void
    {
        // récupération de la liste complète des tags
        $repository = $this->manager->getRepository(Tag::class);
        $tags = $repository->findAll();

        // récupération d'un tag à partir de son id:
        $htmlTag = $repository->find(1);
        $csslTag = $repository->find(2);

        // récupération du troisème élément de la liste complète c à d le tag js
        $jslTag = $tags[2];

        // éléments de code à réutiliser dans nos boucles
        // $htmlTag = $tags[0];
        // $htmlTag->getName();

        // $tags[0]->getName();

        $shortlist = $this->faker->randomElements($tags, 3);

        // données statiques
        $datas = [
            [
                'name' => 'site vitrine',
                'description' => null,
                'clientName' => 'Alice',
                'startDate' => new DateTime('2022-10-01'),
                'checkpointDate' => new DateTime('2022-11-01'),
                'deliveryDate' => new DateTime('2022-12-01'),
                'tags' => [$htmlTag, $csslTag],
            ],

            [
                'name' => 'wordpress',
                'description' => null,
                'clientName' => 'Bob',
                'startDate' => new DateTime('2022-02-01'),
                'checkpointDate' => new DateTime('2022-03-01'),
                'deliveryDate' => new DateTime('2022-04-01'),
                'tags' => [$csslTag, $jslTag],
            ],

            [
                'name' => 'API Rest',
                'description' => null,
                'clientName' => 'Charlie',
                'startDate' => new DateTime('2022-05-01'),
                'checkpointDate' => new DateTime('2022-06-01'),
                'deliveryDate' => new DateTime('2022-07-01'),
                'tags' => [$jslTag],
            ]
        ];

        foreach ($datas as $data) {
            $project = new Project();
            $project->setName($data['name']);
            $project->setDescription($data['description']);
            $project->setClientName($data['clientName']);
            $project->setStartDate($data['startDate']);
            $project->setCheckpointDate($data['checkpointDate']);
            $project->setDeliveryDate($data['deliveryDate']);

            foreach ($data['tags'] as $tag) {
                $project->addTag($tag);
            }

            $this->manager->persist($project);
        }

        $this->manager->flush();

        // données dynamiques
        for ($i = 0; $i < 30; $i++) {
            $project = new Project();

            $words = random_int(3, 5);
            $project->setName($this->faker->sentence($words));

            $words = random_int(5, 15);
            $project->setDescription($this->faker->optional(0.7)->sentence($words));

            $project->setClientName($this->faker->name());
            $project->setStartDate($this->faker->dateTimeBetween('-12 months', '-10 months'));
            $project->setCheckpointDate($this->faker->dateTimeBetween('-10 months', '-8 months'));
            $project->setDeliveryDate($this->faker->dateTimeBetween('-8 months', '-6 months'));

            // on choisit le nombre de tag au hasard entre 1 et 4
            $tagsCount = random_int(1, 4);

            // on choisit ici des tags au hasard depuis la liste complète
            $shortlist = $this->faker->randomElements($tags, $tagsCount);

            // on passe en revue chaque tag de la short liste
            foreach ($shortlist as $tag) {
                // on associe un tag avec le projet
                $project->addTag($tag);
            }


            $this->manager->persist($project);
        }

        $this->manager->flush();
    }

    public function loadStudent(): void
    {
        $repository = $this->manager->getRepository(SchoolYear::class);
        $schoolYears = $repository->findAll();

        $allanTuring = $repository->find(1);
        $johnVonNewman = $repository->find(2);
        $brendanEich = $repository->find(3);

        $repository = $this->manager->getRepository(Project::class);
        $projects = $repository->findAll();

        $siteVitrine = $repository->find(1);
        $wordpress = $repository->find(2);
        $apiRest = $repository->find(3);

        $repository = $this->manager->getRepository(Tag::class);
        $tags = $repository->findAll();

        $html = $repository->find(1);
        $css = $repository->find(2);
        $js = $repository->find(3);

        //données statiques
        $datas = [
            [
                'email' => 'foo@exemple.com',
                'password' => '123',
                'roles' => ['ROLE_USER'],
                'firstname' => 'foo',
                'lastname' => 'Exemple',
                'schoolYear' => $allanTuring,
                'projects' => [$siteVitrine],
                'tags' => [$html, $css],
            ],
            [
                'email' => 'bar@exemple.com',
                'password' => '123',
                'roles' => ['ROLE_USER'],
                'firstname' => 'bar',
                'lastname' => 'Exemple',
                'schoolYear' => $johnVonNewman,
                'projects' => [$wordpress],
                'tags' => [$html, $css],
            ],
            [
                'email' => 'baz@exemple.com',
                'password' => '123',
                'roles' => ['ROLE_USER'],
                'firstname' => 'baz',
                'lastname' => 'Exemple',
                'schoolYear' => $brendanEich,
                'projects' => [$apiRest],
                'tags' => [$js],
            ],
        ];

        foreach ($datas as $data) {
            $user = new User();
            $user->setEmail($data['email']);
            $password = $this->hasher->hashPassword($user, $data['password']);
            $user->setPassword($password);
            $user->setRoles($data['roles']);

            $this->manager->persist($user);

            $student = new Student();
            $student->setFisrtname($data['firstname']);
            $student->setLastName($data['lastname']);
            $student->setSchoolYear($data['schoolYear']);
            $student->setUser($user);

            //Récupération du premier objet de la liste du student
            $project = $data['projects'][0];
            $student->addProject($project);

            foreach ($data['tags'] as $tag) {
                $student->addTag($tag);
            }

            $this->manager->persist($student);
        }

        $this->manager->flush();

        //données dynamiques
        for ($i = 0; $i < 10; $i++) {
            $user = new User();
            $user->setEmail($this->faker->unique()->safeEmail());
            $password = $this->hasher->hashPassword($user, '123');
            $user->setPassword($password);
            $user->setRoles(['ROLE_USER']);

            $this->manager->persist($user);

            $student = new Student();
            $student->setFisrtname($this->faker->firstName());
            $student->setLastName($this->faker->lastName());

            $schoolYear = $this->faker->randomElement($schoolYears);
            $student->setSchoolYear($schoolYear);

            $project = $this->faker->randomElement($projects);
            $student->addProject($project);

            $tagsCount = random_int(1, 4);
            $shortlist = $this->faker->randomElement($tags, $tagsCount);

            foreach ($shortlist as $tag) {
                $student->addTag($tag);
            }

            $student->setUser($user);

            $this->manager->persist($student);
        }

        $this->manager->flush();
    }
}
