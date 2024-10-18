<?php

namespace App\Controller;

use App\Entity\Audit;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

class ErroldaProxyController extends AbstractController
{
    public function __construct(
        private string $swalUrl, 
        private HttpClientInterface $httpClient, 
        private TranslatorInterface $translator,
        private EntityManagerInterface $em,
    ) {}

    public function proxy($query): Response
    {
        $response = $this->httpClient->request(Request::METHOD_GET,$this->swalUrl,[
            'query' => $query,
        ]);
        $contentType = mb_strtolower($response->getHeaders()['content-type'][0]);
        $content = $response->getContent();
        if ( mb_strpos($contentType, 'application/pdf') !== false ) {
            return new Response($content,Response::HTTP_OK,[
                'Content-type' => 'application/pdf', 
                'Content-Disposition' => "attachment; filename=volante.pdf",
                'Content-Length' => mb_strlen($content),
            ]);
        } else if ( mb_strpos($contentType, 'application/json') !== false ) {
            // Ejempo de respuesta erronea: {"dni":"11111111H","msg":"Habitante sin empadronar"}
            $json = json_decode($content, true);
            return $this->render('errolda_proxy/index.html.twig',[
                'msg' => $json['msg'],
                'dni' => $json['dni'],
            ]);
        }
        throw new HttpException(Response::HTTP_INTERNAL_SERVER_ERROR, 'Invalid Content-Type received');
    }

    #[Route('/{_locale}/banakakoa', name: 'app_errolda_banakakoa')]
    public function bakanakoa(Request $request) {
        if ( $request->getSession()->get('giltzaUser') === null ) {
            $request->getSession()->set('targetRoute', 'app_errolda_banakakoa');
            return $this->redirectToRoute('app_giltza');
        }
        $giltzaUser = $request->getSession()->get('giltzaUser');
        $audit = new Audit();
        $audit->setCreatedAt(new \DateTime());
        $audit->fill($giltzaUser);
        $this->em->persist($audit);
        $this->em->flush();
        $query = [
            'psTipo' => '3',
            'psDni' => $giltzaUser['dni'],
            'psEfectos' => 'Individual'
        ];
        return $this->proxy($query);
    }

    #[Route('/{_locale}/historikoa', name: 'app_errolda_historikoa')]
    public function historikoa(Request $request) {
        if ( $request->getSession()->get('giltzaUser') === null ) {
            $request->getSession()->set('targetRoute', 'app_errolda_historikoa');
            return $this->redirectToRoute('app_giltza');
        }
        $giltzaUser = $request->getSession()->get('giltzaUser');
        $audit = new Audit();
        $audit->setCreatedAt(new \DateTime());
        $audit->fill($giltzaUser);
        $this->em->persist($audit);
        $this->em->flush();
        $query = [
            'psTipo' => '9',
            'psDni' => $giltzaUser['dni'],
            'psEfectos' => 'Historico'
        ];
        return $this->proxy($query);
    }
}
