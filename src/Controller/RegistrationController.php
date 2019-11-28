<?php

namespace App\Controller;

use App\Entity\Customer;
use App\Entity\Owner;
use App\Entity\User;
use App\Form\RegistrationFormType;
use App\Security\LoginFormAuthenticator;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Guard\GuardAuthenticatorHandler;

class RegistrationController extends AbstractController
{
    /**
     * @Route("/register", name="app_register")
     */
    public function register(Request $request, UserPasswordEncoderInterface $passwordEncoder, GuardAuthenticatorHandler $guardHandler, LoginFormAuthenticator $authenticator): Response
    {
        if ($this->getUser()) {
            return $this->redirectToRoute('home');
        }
        $user = new User();
        $form = $this->createForm(RegistrationFormType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // encode the plain password
            $user->setPassword(
                $passwordEncoder->encodePassword(
                    $user,
                    $form->get('plainPassword')->getData()
                )
            );

            $customer = new Customer();
            $customer->setFirstname($form->get('firstname')->getData());
            $customer->setFamilyName($form->get('familyName')->getData());
            $customer->setCountry($form->get('country')->getData());

            $isOwner = $request->get('isOwner');
            if ($isOwner === "on") {

                $owner = new Owner();
                $owner->setFirstname($form->get('firstname')->getData());
                $owner->setFamilyName($form->get('familyName')->getData());
                $owner->setAddress($form->get('address')->getData());
                $owner->setCountry($form->get('country')->getData());

                $user->setOwner($owner);
                $user->setCustomer($customer);
                $user->setRoles(array('ROLE_USER', 'ROLE_OWNER'));
            }
            else {
                $user->setCustomer($customer);
                $user->setRoles(array('ROLE_USER'));
            }
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($user);
            $entityManager->flush();

            // do anything else you need here, like send an email

            return $guardHandler->authenticateUserAndHandleSuccess(
                $user,
                $request,
                $authenticator,
                'main' // firewall name in security.yaml
            );
        }

        return $this->render('registration/register.html.twig', [
            'registrationForm' => $form->createView(),
        ]);
    }
}
