<?php
namespace App\Controller\admin;

use App\Form\FormationType;
use App\Repository\FormationRepository;
use App\Repository\CategorieRepository;
use App\Entity\Formation;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\ORM\EntityManagerInterface;
 
 
/**
 * Contrôleur de la partie administration des formations
 *
 * @author Ethan MENAGE [ethanmng.pro@gmail.com]
 */
#[Route('/admin/formations')]
class AdminFormationsController extends AbstractController
{
    /**
     * 
     * @var FormationRepository
     */
    private FormationRepository $formationRepository;
 
    /**
     * 
     * @var CategorieRepository
     */
    private CategorieRepository $categorieRepository;
    
    
    /**
     * Template path
     * @var String
     */
    private const TEMPLATE_PATH = "admin/formations/main.html.twig";
 
    public function __construct(FormationRepository $formationRepository, CategorieRepository $categorieRepository)
    {
        $this->formationRepository = $formationRepository;
        $this->categorieRepository = $categorieRepository;
    }
 
    #[Route('/', name: 'admin.formations')]
    public function index(): Response
    {
        $formations = $this->formationRepository->findAll();
        $categories = $this->categorieRepository->findAll();
        
        return $this->render(self::TEMPLATE_PATH, [
            'formations' => $formations,
            'categories' => $categories,
        ]);
    }
 
    #[Route('/{id}/edit', name: 'admin.formations.edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Formation $formation, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(FormationType::class, $formation);
        $form->handleRequest($request);
 
        if ($form->isSubmitted() && $form->isValid()) {
            $this->addFlash('success', 'La formation a été modifiée avec succès !');
            $entityManager->flush();
            return $this->redirectToRoute('admin.formations');
        }
 
        return $this->render('admin/formations/edit.html.twig', [
            'formation' => $formation,
            'form' => $form,
        ]);
    }
 
    #[Route('/{id}/delete', name: 'admin.formations.delete', methods: ['POST'])]
    public function delete(Request $request, Formation $formation, EntityManagerInterface $entityManager): Response
    {
        
        if ($this->isCsrfTokenValid('delete'.$formation->getId(), $request->request->get('_token'))) {
            $entityManager->remove($formation);
            $this->addFlash('success', 'La formation a été supprimée avec succès !');
            $entityManager->flush();
        }
 
        return $this->redirectToRoute('admin.formations');
    }
     
    #[Route('/addformation', name: 'admin.formations.create', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $formation = new Formation();
        $form = $this->createForm(FormationType::class, $formation);
        $form->handleRequest($request);
 
        if ($form->isSubmitted() && $form->isValid()) {
            $this->addFlash('success', 'La formation a été créée avec succès !');
            $entityManager->persist($formation);
            $entityManager->flush();
 
            return $this->redirectToRoute('admin.formations');
        }
 
        return $this->render('admin/formations/addformation.html.twig', [
            'formation' => $formation,
            'form' => $form,
        ]);
    }
     
    #[Route('/tri/{champ}/{ordre}/{table}', name: 'admin.formations.sort')]
    public function sort(string $champ, string $ordre, string $table = ''): Response
    {
         $formations = $this->formationRepository->findAllOrderBy($champ, $ordre, $table);
         $categories = $this->categorieRepository->findAll();
         
        return $this->render(self::TEMPLATE_PATH, [
            'formations' => $formations,
            'categories' => $categories,
        ]);
    }

    #[Route('/recherche/{champ}/{table}', name: 'admin.formations.findallcontain')]
    public function findAllContain(string $champ, Request $request, string $table = ''): Response
    {
        $valeur = $request->get('recherche');
        $formations = $this->formationRepository->findByContainValue($champ, $valeur, $table);
        $categories = $this->categorieRepository->findAll();
        return $this->render(self::TEMPLATE_PATH, [
            'formations' => $formations,
            'categories' => $categories,
            'valeur' => $valeur,
            'champ' => $champ,
            'table' => $table,
        ]);
    }
 
    #[Route('/{id}', name: 'admin.formations.showone')]
    public function showOne(int $id): Response
    {
        $formation = $this->formationRepository->find($id);
        $categories = $this->categorieRepository->findAll();
        return $this->render('admin/formations/formation.html.twig', [
            'formation' => $formation,
            'categories'=> $categories,
        ]);
    }
}