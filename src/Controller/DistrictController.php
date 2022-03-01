<?php

namespace App\Controller;

use App\Entity\District;
use App\Form\DistrictType;
use App\Form\FilterType;
use App\Repository\DistrictRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/')]
class DistrictController extends AbstractController
{
    private DistrictRepository $districtRepository;

    public function __construct(DistrictRepository $districtRepository)
    {
        $this->districtRepository = $districtRepository;
    }

    /**
     * @Route("/", name="app_district_homepage")
     * @param string $orderBy
     * @return Response
     */
    public function index(Request $request): Response
    {
        $form = $this->createForm(FilterType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $formData = $form->getData();
            return $this->redirectToRoute(
                'app_district_homepage_filtered',
                ['column' => $formData['column'], 'value' => $formData['value']]
            );
        }

        return $this->render('district/index.html.twig', [
            'districts' => $this->districtRepository->findAll(),
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("/orderby/{orderBy}", name="app_district_homepage_sorted")
     * @param string $orderBy
     * @return Response
     */
    public function indexSorted(Request $request, string $orderBy): Response
    {
        $form = $this->createForm(FilterType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $formData = $form->getData();
            return $this->redirectToRoute(
                'app_district_homepage_filtered',
                ['column' => $formData['column'], 'value' => $formData['value']]
            );
        }

        if (!in_array($orderBy, ['name','population','city','area'])) {
            $orderBy = 'name';
        }
        return $this->render('district/index.html.twig', [
            'districts' => $this->districtRepository->findBy([], [$orderBy => 'ASC']),
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("/filter/{column}/{value}", name="app_district_homepage_filtered")
     * @param string $column
     * @param string $value
     * @return Response
     */
    public function indexFiltered(Request $request, string $column, string $value): Response
    {
        $form = $this->createForm(FilterType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $formData = $form->getData();
            return $this->redirectToRoute(
                'app_district_homepage_filtered',
                ['column' => $formData['column'], 'value' => $formData['value']]
            );
        }

        if (!in_array($column, ['name','population','city','area'])) {
            $column = 'name';
        }
        return $this->render('district/index.html.twig', [
            'districts' => $this->districtRepository->findByLike($column, $value),
            'form' => $form->createView()
        ]);
    }

    #[Route('/new', name: 'app_district_new', methods: ['GET', 'POST'])]
    public function new(Request $request): Response
    {
        $district = new District();
        $form = $this->createForm(DistrictType::class, $district);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->districtRepository->add($district);
            return $this->redirectToRoute('app_district_homepage', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('district/new.html.twig', [
            'district' => $district,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_district_show', methods: ['GET'])]
    public function show(District $district): Response
    {
        return $this->render('district/show.html.twig', [
            'district' => $district,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_district_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, District $district): Response
    {
        $form = $this->createForm(DistrictType::class, $district);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->districtRepository->add($district);
            return $this->redirectToRoute('app_district_homepage', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('district/edit.html.twig', [
            'district' => $district,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_district_delete', methods: ['POST'])]
    public function delete(Request $request, District $district): Response
    {
        if ($this->isCsrfTokenValid('delete' . $district->getId(), $request->request->get('_token'))) {
            $this->districtRepository->remove($district);
        }

        return $this->redirectToRoute('app_district_homepage', [], Response::HTTP_SEE_OTHER);
    }
}
