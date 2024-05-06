<?php

namespace App\Security;

use App\Form\LoginFormType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Security\Http\Util\TargetPathTrait;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Http\Authenticator\Passport\Passport;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\UserBadge;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\CsrfTokenBadge;
use Symfony\Component\Security\Http\Authenticator\AbstractLoginFormAuthenticator;
use Symfony\Component\Security\Http\Authenticator\Passport\Credentials\PasswordCredentials;

class AdminCustomAuthenticator extends AbstractLoginFormAuthenticator
{
    use TargetPathTrait;

    public const LOGIN_ROUTE = 'app_login';

    private UrlGeneratorInterface $urlGenerator;

    public function __construct(UrlGeneratorInterface $urlGenerator, FormFactoryInterface $formFactory)
    {
        $this->urlGenerator = $urlGenerator;
        $this->formFactory = $formFactory;

    }

    public function authenticate(Request $request): Passport
    {
        //$email = $request->request->get('email', '');
        $loginForm = $this->formFactory->create(LoginFormType::class);
        $loginForm->handleRequest($request);

        $email = $loginForm->get('email')->getData();

        $request->getSession()->set(Security::LAST_USERNAME, $email);

        return new Passport(
            new UserBadge($email),
            //new PasswordCredentials($request->request->get('password', '')),
            new PasswordCredentials($loginForm->get('password')->getData()),
            [
                //new CsrfTokenBadge('authenticate', $request->get('_csrf_token')),
                new CsrfTokenBadge('authenticate', $request->get('_csrf_token')),
                //new RememberMeBadge(),
            ]
        );
    }

    public function onAuthenticationSuccess(Request $request, TokenInterface $token, string $firewallName): ?Response
    {
        if ($targetPath = $this->getTargetPath($request->getSession(), $firewallName)) {
            return new RedirectResponse($targetPath);
        }

        // For example:
        //$url = $this->urlGenerator->generate('app_admin_liste') . '#dashboard';
        //return new RedirectResponse($url);
        return new RedirectResponse($this->urlGenerator->generate('app_admin_liste'));
        //throw new \Exception('TODO: provide a valid redirect inside '.__FILE__);
    }

    protected function getLoginUrl(Request $request): string
    {
        return $this->urlGenerator->generate(self::LOGIN_ROUTE);
    }
}
