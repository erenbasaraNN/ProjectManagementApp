<?php

namespace App\Security;

use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Http\Authenticator\AbstractLoginFormAuthenticator;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\RememberMeBadge;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\UserBadge;
use Symfony\Component\Security\Http\Authenticator\Passport\Credentials\PasswordCredentials;
use Symfony\Component\Security\Http\Authenticator\Passport\Passport;
use Symfony\Component\Security\Http\Util\TargetPathTrait;

class AppAuthenticator extends AbstractLoginFormAuthenticator
{
    use TargetPathTrait;

    private UrlGeneratorInterface $urlGenerator;

    public function __construct(UrlGeneratorInterface $urlGenerator)
    {
        $this->urlGenerator = $urlGenerator;
    }

    public function supports(Request $request): bool
    {
        // Authenticator should only support POST requests to the login route
        return $request->attributes->get('_route') === 'app_login' && $request->isMethod('POST');
    }


    public function authenticate(Request $request): Passport
    {
        $email = $request->request->get('email', '');
        error_log("authenticate() called. Email: " . $email);

        return new Passport(
            new UserBadge($email),
            new PasswordCredentials($request->request->get('password', '')),
            [
                new RememberMeBadge(),
            ]
        );
    }

    public function onAuthenticationSuccess(Request $request, TokenInterface $token, string $firewallName): ?RedirectResponse
    {
        error_log("Authentication success: User is authenticated.");

        if ($targetPath = $this->getTargetPath($request->getSession(), $firewallName)) {
            error_log("Redirecting to target path: " . $targetPath);
            return new RedirectResponse($targetPath);
        }

        $homeUrl = $this->urlGenerator->generate('app_home');
        error_log("Redirecting to home: " . $homeUrl);

        return new RedirectResponse($homeUrl);
    }


    protected function getLoginUrl(Request $request): string
    {
        return $this->urlGenerator->generate('app_login');
    }
}
