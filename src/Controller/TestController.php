<?php

namespace App\Controller;

use App\Entity\SchoolYear;
use App\Entity\Tag;
use DateTime;
use Doctrine\Persistence\ManagerRegistry;
use Exception;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/test')]
class TestController extends AbstractController
{
    #[Route('/tag', name: 'app_test_tag')]
    public function tag(ManagerRegistry $doctrine): Response
    {
        $em = $doctrine->getManager();
        $tagrepository = $em->getRepository((Tag::class));

        // création d'un nouvel objet
        $foo = new Tag();
        $foo->setName('Foo');
        $foo->setDescription('Foo bar baz');
        $em->persist($foo);
        $em->flush();

        try {
            $em->flush();
        } catch (Exception $e) {
            // gérer l'erreur
            dump($e->getMessage());
        }

        // récupération de l'objet dont l'id est égale à 1
        $tag = $tagrepository->find(1);

        // récupération et suppréssion de l'objet dont l'id est égale à 15
        $tag15 = $tagrepository->find(15);

        if ($tag15) {
            $em->remove($tag15);
            $em->flush();
        }

        // récupération et suppréssion de l'objet dont l'id est égale à 4
        $tag4 = $tagrepository->find(4);

        $tag4->setName('Python');
        $tag4->setDescription(null);

        // pas besoin d'appler persist() si l'objet est seulement modifié   
        $em->flush();

        // récupération de la liste complète des objets
        $tags = $tagrepository->findAll();

        // récupération d'un tag dont le nom est CSS
        $cssTag = $tagrepository->findOneBy([
            'name' => 'CSS'
        ]);

        $notNullDescriptionTags = $tagrepository->findByNotNullDescription();

        $nullDescriptionTags = $tagrepository->findByNullDescription();

        $keywordTags1 = $tagrepository->findByKeyword('html');

        $title = 'Test des tags';

        return $this->render('test/tag.html.twig', [
            'title' => $title,
            'tags' => $tags,
            'tag' => $tag,
            'cssTag' => $cssTag,
            'notNullDescription' => $notNullDescriptionTags,
            'nullDescription' => $nullDescriptionTags,
            'keywordTags1' => $keywordTags1,
        ]);
    }

    #[Route('/school-year', name: 'app_test_schoolyear')]
    public function schoolYear(ManagerRegistry $doctrine): Response
    {
        $em = $doctrine->getManager();
        $studentrepository = $em->getRepository((SchoolYear::class));

        // création d'un nouvel objet
        $bar = new SchoolYear();
        $bar->setName('Bar');
        $bar->setDescription('Foo bar baz');
        $bar->setStartDate(new DateTime("01-05-2023"));
        $bar->setEndDate(new DateTime("04-10-2023"));
        $em->persist($bar);
        $em->flush();

        try {
            $em->flush();
        } catch (Exception $e) {
            // gérer l'erreur
            dump($e->getMessage());
        }

        // récupération de l'objet dont l'id est égale à 1
        $schoolYear = $studentrepository->find(1);

        // récupération et suppréssion de l'objet dont l'id est égale à 15
        $schoolYear5 = $studentrepository->find(67);

        if ($schoolYear5) {
            $em->remove($schoolYear5);
            $em->flush();
        }

        // récupération et suppréssion de l'objet dont l'id est égale à 4
        $schoolYear10 = $studentrepository->find(10);

        $schoolYear10->setName('Baz');
        $schoolYear10->setDescription(null);

        // pas besoin d'appler persist() si l'objet est seulement modifié   
        $em->flush();

        // récupération de la liste complète des objets
        $schoolYears = $studentrepository->findAll();

        $title = 'Test des school-Year';

        return $this->render('test/school-year.html.twig', [
            'title' => $title,
            'schoolYears' => $schoolYears,
            'schoolYear' => $schoolYear,
        ]);
    }
}
