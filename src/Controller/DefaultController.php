<?php

namespace App\Controller;


use App\Entity\User;
use App\Entity\Postes;
use App\Entity\Report;
use App\Form\PostType;
use App\Entity\UserUser;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bridge\Doctrine\ManagerRegistry;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
class DefaultController extends AbstractController
{
   #[Route('/', name: 'app_default')]
public function index(EntityManagerInterface $entityManager, Request $request): Response
{
    // Check if the user is not authenticated or not verified
    if (!$this->getUser() || ($this->getUser() instanceof User && $this->getUser()->getEtat() === 'NOT_VERIFIED')) {
        // Redirect to the login page
        return $this->redirectToRoute('app_login');
    }
   
    // Get the connected user
    $connectedUser = $this->getUser();

    // Fetch friends of the connected user with accepted friend requests
    $friendshipsAccept = $entityManager->getRepository(UserUser::class)->findBy([
        'user_target' => $connectedUser,
        'Etat' => 'ami',
    ]);

    // Fetch friends of the connected user where the connected user initiated the friend request
    $friendshipsInitiated = $entityManager->getRepository(UserUser::class)->findBy([
        'user_source' => $connectedUser,
        'Etat' => 'ami',
    ]);

    // Merge the arrays of friendships
    $friendships = array_merge($friendshipsAccept, $friendshipsInitiated);

    // Extract friend ids
    $friendIds = array_map(function ($friendship) {
        if ($friendship->getUserSource()->getId() == $this->getUser()->getId()) {
            return $friendship->getUserTarget()->getId();
        } elseif ($friendship->getUserTarget()->getId() == $this->getUser()->getId()) {
            return $friendship->getUserSource()->getId();
        }
    }, $friendships);
    $post = new Postes();
    $post->setUserId($connectedUser);
    $form = $this->createForm(PostType::class,$post);


    // Handle form submission
    $form->handleRequest($request);
    if ($form->isSubmitted() && $form->isValid()) {
        $imageFile = $form->get('image')->getData();
        if ($imageFile) {
            $imageData = file_get_contents($imageFile->getPathname());
            $post->setPhoto($imageData);
        }
        $post = $form->getData();
        $entityManager->persist($post);
        $entityManager->flush();

        // Redirect to the same page to avoid resubmission on page refresh
        return $this->redirectToRoute('app_default');
    }
    // Remove null values from the friend ids array
    $friendIds = array_filter($friendIds);

    // Fetch posts for the connected user's friends
    $connectedUserPosts = $entityManager->getRepository(Postes::class)->findBy(['User_id' => $connectedUser->getId()]);

    // Fetch posts for the connected user's friends
    $friendPosts = $entityManager->getRepository(Postes::class)->findBy(['User_id' => $friendIds]);

    // Combine the connected user's posts and friend's posts
    $posts = array_merge($connectedUserPosts, $friendPosts);
    // Render the template with the posts
    return $this->render('User/profile.html.twig', ['form' => $form->createView(),'posts' => $posts ]);
}


    #[Route('/friends', name: 'app_freinds')]
    public function friends(EntityManagerInterface $entityManager): Response
    {
        
    // Get the connected user
    $loggedInUser = $this->getUser();

    // Check if the user is authenticated
    if (!$loggedInUser) {
        // Redirect to the login page or handle as appropriate
        return $this->redirectToRoute('app_login');
    }
    
    // Get friend requests for the connected user
    $friendsREQ = $entityManager->getRepository(UserUser::class)->findBy([
      'Etat' => 'ami',
      'user_source'=>$loggedInUser
    ]);
    $friendsAQC = $entityManager->getRepository(UserUser::class)->findBy([
        'user_target' => $loggedInUser, 
        'Etat' => 'ami',
      ]);
      // Merge the arrays of friendships
    $friendships = array_merge($friendsREQ, $friendsAQC);

    // Extract friend ids
    $friendIds = array_map(function ($friendship) {
        if ($friendship->getUserSource()->getId() == $this->getUser()->getId()) {
            return $friendship->getUserTarget()->getId();
        } elseif ($friendship->getUserTarget()->getId() == $this->getUser()->getId()) {
            return $friendship->getUserSource()->getId();
        }
    }, $friendships);

    return $this->render('User/followers.html.twig', ['friends' => $friendships]);
    }
    #[Route('/alerts', name: 'app_alerts')]
    public function activity(): Response
    {
        return $this->render('User/alerts.html.twig');
    }
    #[Route('/setting', name: 'app_setting')]
    public function setting(): Response
    {
        return $this->render('User/setting.html.twig');
    }
    
  
#[Route('/requests', name: 'app_requests')]
public function requests(EntityManagerInterface $entityManager): Response
{
    // Get the connected user
    $loggedInUser = $this->getUser();

    // Check if the user is authenticated
    if (!$loggedInUser) {
        // Redirect to the login page or handle as appropriate
        return $this->redirectToRoute('app_login');
    }

    // Get friend requests for the connected user
    $friendRequests = $entityManager->getRepository(UserUser::class)->findBy([
        'user_target' => $loggedInUser,
        'Etat' => 'en_attent',
    ]);

    return $this->render('User/requests.html.twig', ['friendRequests' => $friendRequests]);
}
    #[Route('/notverif', name: 'not_verified')]
    public function notverified(): Response
    {    
        return $this->render('security/notverifed.html.twig');

    }
    #[Route('/admin', name: 'admin')]
    public function admin(UserRepository $userRepository): Response
    {    
        
        if (!$this->getUser() || !in_array('ROLE_ADMIN', $this->getUser()->getRoles())) {
            return $this->redirectToRoute('app_login');
    }
        $users = $userRepository->findAll();

        return $this->render('admin/adminhome.html.twig', ['users' => $users]);

    }
    #[Route('/adminreports', name: 'adminreports')]
public function adminReports(EntityManagerInterface $entityManager): Response
{
    // Query to get users with 3 or more reports on their posts
    $usersWithReports = $entityManager->getRepository(User::class)
        ->createQueryBuilder('u')
        ->select('u')
        ->join(Postes::class, 'p', 'WITH', 'p.User_id = u.id') // Assuming 'User_id' is the field in Postes representing the association
        ->join(Report::class, 'r', 'WITH', 'r.postee = p')
        ->groupBy('u.id')
        ->having('COUNT(r.id) >= 3')
        ->getQuery()
        ->getResult();

    return $this->render('admin/reportDash.html.twig', [
        'usersWithReports' => $usersWithReports,
    ]);
}

    
    #[Route('/verify/{id}', name: 'verify')]
    public function verifyUser($id, Request $request, UserRepository $userRepository,EntityManagerInterface $entityManager): Response
    {
        // Find the user by ID
        $user = $userRepository->findOneBy(['id' => $id]);
    
        // Check if the user exists
        if (!$user) {
            // Handle the case where the user is not found (you may redirect or show an error)
            // For example, redirect to an error page:
            return $this->redirectToRoute('admin');
        }
    
        // Update the user's etat to VERIFIED
        $user->setEtat('VERIFIED');
    
        // Persist the changes to the database
        $entityManager->persist($user);
        $entityManager->flush();
    
        // Optionally, you can add a flash message to notify the user
        $this->addFlash('success', 'User verified successfully!');
    
        // Redirect to a specific route or page after verification
        return $this->redirectToRoute('admin');
    }
    #[Route('/unverify/{id}', name: 'unverify')]

    public function unverifyUser($id, Request $request, UserRepository $userRepository,EntityManagerInterface $entityManager): Response
    {
        // Find the user by ID
        $user = $userRepository->findOneBy(['id' => $id]);
    
        // Check if the user exists
        if (!$user) {
            // Handle the case where the user is not found (you may redirect or show an error)
            // For example, redirect to an error page:
            return $this->redirectToRoute('admin');
        }
    
        // Update the user's etat to VERIFIED
        $user->setEtat('NOT_VERIFIED');
    
        // Persist the changes to the database
        $entityManager->persist($user);
        $entityManager->flush();
    
        // Optionally, you can add a flash message to notify the user
        $this->addFlash('success', 'User verified successfully!');
    
        // Redirect to a specific route or page after verification
        return $this->redirectToRoute('admin');
    }
   
#[Route('/affiche/{name}', name: 'affiche')]
public function affiche($name, UserRepository $userRepository, EntityManagerInterface $entityManager): Response
{    
    $users = $userRepository->createQueryBuilder('u')
        ->where('u.name LIKE :name')
        ->setParameter('name', '%'.$name.'%')
        ->getQuery()
        ->getResult();

    // Get the logged-in user
    $loggedInUser = $this->getUser();

    // Check if the logged-in user exists
    if ($loggedInUser) {
        // Check if the relationship exists in the UserUser table
        foreach ($users as $user) {
            $existingRelation = $entityManager->getRepository(UserUser::class)->findOneBy([
                'user_source' => $loggedInUser,
                'user_target' => $user,
                'Etat' => 'en_attent',
            ]);

            // Add a property to each user indicating whether the button should be clickable
            $user->isButtonClickable = !$existingRelation;
        }
    }

    return $this->render('User/affiche.html.twig', ['users' => $users]);
}
    #[Route('/add/{user_target_id}', name: 'app_add')]
    public function add($user_target_id, EntityManagerInterface $entityManager): RedirectResponse
    {
        // Check if the user is authenticated
        $user = $this->getUser();
        if (!$user) {
            // Redirect to the login page or handle as appropriate
            return $this->redirectToRoute('app_login');
        }

        // Fetch the User entities by their IDs
        $userSource = $entityManager->getRepository(User::class)->find($user->getId());
        $userTarget = $entityManager->getRepository(User::class)->find($user_target_id);

        // Check if both users exist
        if (!$userSource || !$userTarget) {
            // Redirect or handle the case where one or both users are not found
            return $this->redirectToRoute('app_default');
        }

        // Create a new UserUser entity and set the relationships
        $userUser = new UserUser();
        $userUser->setUserSource($userSource);
        $userUser->setUserTarget($userTarget);
        $userUser->setEtat("en_attent");
        // You can set other properties if needed

        // Persist the UserUser entity
        $entityManager->persist($userUser);
        $entityManager->flush();

        // Redirect to the home page or any other route as needed
        return $this->redirectToRoute('app_default');
    }
    #[Route('/accept-invitation/{id}', name: 'accept_invitation')]
public function acceptInvitation($id, EntityManagerInterface $entityManager): Response
{
    // Find the UserUser entity by ID
    $invitation = $entityManager->getRepository(UserUser::class)->find($id);

    // Check if the invitation exists
    if (!$invitation) {
        // Handle the case where the invitation is not found
        // You may redirect or show an error, for example:
        return $this->redirectToRoute('app_requests');
    }

    // Check if the user accepting the invitation is the target user of the invitation
    $loggedInUser = $this->getUser();
    if ($loggedInUser !== $invitation->getUserTarget()) {
        // Handle the case where the logged-in user is not the target of the invitation
        // You may redirect or show an error, for example:
        return $this->redirectToRoute('app_requests');
    }

    // Update the Etat to "ami"
    $invitation->setEtat('ami');

    // Persist the changes to the database
    $entityManager->persist($invitation);
    $entityManager->flush();

    // Optionally, you can add a flash message to notify the user
    $this->addFlash('success', 'Invitation accepted successfully!');

    // Redirect to the requests page or any other route as needed
    return $this->redirectToRoute('app_requests');
}
#[Route('/delete-invitation/{id}', name: 'delete_invitation')]
public function deleteInvitation($id, EntityManagerInterface $entityManager): Response
{
    // Find the UserUser entity by ID
    $invitation = $entityManager->getRepository(UserUser::class)->find($id);

    // Check if the invitation exists
    if (!$invitation) {
        // Handle the case where the invitation is not found
        // You may redirect or show an error, for example:
        return $this->redirectToRoute('app_default');
    }

    // Check if the user deleting the invitation is the target user of the invitation
    $loggedInUser = $this->getUser();
    if ($loggedInUser !== $invitation->getUserTarget()) {
        // Handle the case where the logged-in user is not the target of the invitation
        // You may redirect or show an error, for example:
        return $this->redirectToRoute('app_default');
    }

    // Remove the UserUser entity
    $entityManager->remove($invitation);

    // Flush the changes to the database
    $entityManager->flush();

    // Optionally, you can add a flash message to notify the user
    $this->addFlash('success', 'Invitation deleted successfully!');

    // Redirect to the requests page or any other route as needed
    return $this->redirectToRoute('app_requests');
}

  
#[Route('/report/{reporter_id}/{postee}', name: 'report')]
public function report($reporter_id,$postee, Request $request,EntityManagerInterface $entityManager): Response
{
    $alreadyreported = $entityManager->getRepository(report::class)->findOneBy([
        'reporter' => $reporter_id,
        'postee' => $postee,
    ]);
    if(!empty($alreadyreported)){
        $this->addFlash('danger', 'you already reported this post!');
        return $this->redirectToRoute('app_default');
    }
    else {
    $reporter= $entityManager->getRepository(User::class)->find($reporter_id);
    $poste= $entityManager->getRepository(Postes::class)->find($postee);
    $reportt=new Report();
    $reportt->setReporter($reporter);
    $reportt->setPostee($poste);
    $entityManager->persist($reportt);
    $entityManager->flush();

    $this->addFlash('success', 'Reported sucuffly!');

    // Redirect to a specific route or page after verification
    return $this->redirectToRoute('app_default');
}
}
}
