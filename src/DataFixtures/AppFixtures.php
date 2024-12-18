<?php /** @noinspection PhpUnhandledExceptionInspection */

namespace App\DataFixtures;

use App\Entity\Movie;
use App\Entity\Season;
use App\Entity\Serie;
use App\Entity\Episode;
use App\Entity\User;
use App\Entity\Category;
use App\Entity\Language;
use App\Entity\Media;
use App\Entity\Playlist;
use App\Entity\Subscription;
use App\Entity\Comment;
use App\Entity\SubscriptionHistory;
use App\Entity\PlaylistSubscription;
use App\Entity\WatchHistory;
use App\Entity\PlaylistMedia;

use App\Enum\CommentStatusEnum;
use App\Enum\UserAccountStatusEnum;

use DateTime;
use DateTimeImmutable;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Faker\Factory as Faker;

class AppFixtures extends Fixture
{
    public const MAX_USERS = 10;
    public const MAX_MEDIA = 100;
    public const MAX_SUBSCRIPTIONS = 3;
    public const MAX_SEASONS = 5;
    public const MAX_EPISODES = 10;

    public const MAX_PLAYLISTS_PER_USER = 3;
    public const MAX_MEDIA_PER_PLAYLIST = 3;
    public const MAX_LANGUAGE_PER_MEDIA = 3;
    public const MAX_CATEGORY_PER_MEDIA = 3;
    public const MAX_SUBSCRIPTIONS_HISTORY_PER_USER = 3;
    public const MAX_COMMENTS_PER_MEDIA = 10;
    public const MAX_PLAYLIST_SUBSCRIPTION_PER_USERS = 3;

    private $faker;

    public function __construct()
    {
        $this->faker = Faker::create();
    }

    public function load(ObjectManager $manager): void
    {
        $users = [];
        $categories = [];
        $languages = [];
        $medias = [];
        $playlists = [];
        $subscriptions = [];

        $this->createUsers($manager, $users);
        $this->createCategories($manager, $categories);
        $this->createLanguages($manager, $languages);
        $this->createMedias($manager, $medias);

        $this->createPlaylists($manager, $users, $playlists);
        $this->createSubscriptions($manager, $users, $subscriptions);
        $this->createComments($manager, $medias, $users);
        $this->createSubscriptionHistories($manager, $users, $subscriptions);
        $this->createPlaylistSubscriptions($manager, $users, $playlists);
        $this->createWatchHistories($manager, $users, $medias);
        $this->createPlaylistMedias($manager, $medias, $playlists);

        $this->linkMediaToCategories($medias, $categories);
        $this->linkMediaToLanguages($medias, $languages);

        $manager->flush();
    }

    //
    // Independent create methods
    //

    protected function createUsers(ObjectManager $manager, array &$users): void
    {
        for ($userNumber = 0; $userNumber < self::MAX_USERS; $userNumber++) {
            $user = new User();
            $user->setUsername($this->faker->userName);
            $user->setEmail($this->faker->email);
            $user->setPlainPassword('password');
            $user->setRoles(['ROLE_USER']);
            $user->setAccountStatus(UserAccountStatusEnum::ACTIVE);
            $user->setProfilePicture('https://i.pravatar.cc/150?u='.$user->getUsername());

            $manager->persist($user);
            $users[] = $user;
        }
    }

    protected function createCategories(ObjectManager $manager, array &$categories): void
    {
        $categoriesData = [
            ['name' => 'Aventure', 'label' => 'aventure', 'icon' => '<path stroke-linecap="round" stroke-linejoin="round" d="m6.115 5.19.319 1.913A6 6 0 0 0 8.11 10.36L9.75 12l-.387.775c-.217.433-.132.956.21 1.298l1.348 1.348c.21.21.329.497.329.795v1.089c0 .426.24.815.622 1.006l.153.076c.433.217.956.132 1.298-.21l.723-.723a8.7 8.7 0 0 0 2.288-4.042 1.087 1.087 0 0 0-.358-1.099l-1.33-1.108c-.251-.21-.582-.299-.905-.245l-1.17.195a1.125 1.125 0 0 1-.98-.314l-.295-.295a1.125 1.125 0 0 1 0-1.591l.13-.132a1.125 1.125 0 0 1 1.3-.21l.603.302a.809.809 0 0 0 1.086-1.086L14.25 7.5l1.256-.837a4.5 4.5 0 0 0 1.528-1.732l.146-.292M6.115 5.19A9 9 0 1 0 17.18 4.64M6.115 5.19A8.965 8.965 0 0 1 12 3c1.929 0 3.716.607 5.18 1.64" />'],
            ['name' => 'Action', 'label' => 'action', 'icon' => '<path stroke-linecap="round" stroke-linejoin="round" d="m3.75 13.5 10.5-11.25L12 10.5h8.25L9.75 21.75 12 13.5H3.75Z" />'],
            ['name' => 'Comedy', 'label' => 'comedy', 'icon' => '<path stroke-linecap="round" stroke-linejoin="round" d="M15.182 15.182a4.5 4.5 0 0 1-6.364 0M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0ZM9.75 9.75c0 .414-.168.75-.375.75S9 10.164 9 9.75 9.168 9 9.375 9s.375.336.375.75Zm-.375 0h.008v.015h-.008V9.75Zm5.625 0c0 .414-.168.75-.375.75s-.375-.336-.375-.75.168-.75.375-.75.375.336.375.75Zm-.375 0h.008v.015h-.008V9.75Z" />'],
            ['name' => 'Drama', 'label' => 'drama', 'icon' => '<path stroke-linecap="round" stroke-linejoin="round" d="M15.182 16.318A4.486 4.486 0 0 0 12.016 15a4.486 4.486 0 0 0-3.198 1.318M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0ZM9.75 9.75c0 .414-.168.75-.375.75S9 10.164 9 9.75 9.168 9 9.375 9s.375.336.375.75Zm-.375 0h.008v.015h-.008V9.75Zm5.625 0c0 .414-.168.75-.375.75s-.375-.336-.375-.75.168-.75.375-.75.375.336.375.75Zm-.375 0h.008v.015h-.008V9.75Z" />'],
            ['name' => 'Horror', 'label' => 'horror', 'icon' => '<path stroke-linecap="round" stroke-linejoin="round" d="M12 12.75c1.148 0 2.278.08 3.383.237 1.037.146 1.866.966 1.866 2.013 0 3.728-2.35 6.75-5.25 6.75S6.75 18.728 6.75 15c0-1.046.83-1.867 1.866-2.013A24.204 24.204 0 0 1 12 12.75Zm0 0c2.883 0 5.647.508 8.207 1.44a23.91 23.91 0 0 1-1.152 6.06M12 12.75c-2.883 0-5.647.508-8.208 1.44.125 2.104.52 4.136 1.153 6.06M12 12.75a2.25 2.25 0 0 0 2.248-2.354M12 12.75a2.25 2.25 0 0 1-2.248-2.354M12 8.25c.995 0 1.971-.08 2.922-.236.403-.066.74-.358.795-.762a3.778 3.778 0 0 0-.399-2.25M12 8.25c-.995 0-1.97-.08-2.922-.236-.402-.066-.74-.358-.795-.762a3.734 3.734 0 0 1 .4-2.253M12 8.25a2.25 2.25 0 0 0-2.248 2.146M12 8.25a2.25 2.25 0 0 1 2.248 2.146M8.683 5a6.032 6.032 0 0 1-1.155-1.002c.07-.63.27-1.222.574-1.747m.581 2.749A3.75 3.75 0 0 1 15.318 5m0 0c.427-.283.815-.62 1.155-.999a4.471 4.471 0 0 0-.575-1.752M4.921 6a24.048 24.048 0 0 0-.392 3.314c1.668.546 3.416.914 5.223 1.082M19.08 6c.205 1.08.337 2.187.392 3.314a23.882 23.882 0 0 1-5.223 1.082" />'],
            ['name' => 'Thriller', 'label' => 'thriller', 'icon' => '<path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 0 1 0-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178Z" /><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" />'],
            ['name' => 'Romance', 'label' => 'romance', 'icon' => '<path stroke-linecap="round" stroke-linejoin="round" d="M21 8.25c0-2.485-2.099-4.5-4.688-4.5-1.935 0-3.597 1.126-4.312 2.733-.715-1.607-2.377-2.733-4.313-2.733C5.1 3.75 3 5.765 3 8.25c0 7.22 9 12 9 12s9-4.78 9-12Z" />'],
            ['name' => 'Sci-Fi', 'label' => 'sci-fi', 'icon' => '<path stroke-linecap="round" stroke-linejoin="round" d="M15.59 14.37a6 6 0 0 1-5.84 7.38v-4.8m5.84-2.58a14.98 14.98 0 0 0 6.16-12.12A14.98 14.98 0 0 0 9.631 8.41m5.96 5.96a14.926 14.926 0 0 1-5.841 2.58m-.119-8.54a6 6 0 0 0-7.381 5.84h4.8m2.581-5.84a14.927 14.927 0 0 0-2.58 5.84m2.699 2.7c-.103.021-.207.041-.311.06a15.09 15.09 0 0 1-2.448-2.448 14.9 14.9 0 0 1 .06-.312m-2.24 2.39a4.493 4.493 0 0 0-1.757 4.306 4.493 4.493 0 0 0 4.306-1.758M16.5 9a1.5 1.5 0 1 1-3 0 1.5 1.5 0 0 1 3 0Z" />'],
            ['name' => 'Fantasy', 'label' => 'fantasy', 'icon' => '<path stroke-linecap="round" stroke-linejoin="round" d="M9.75 3.104v5.714a2.25 2.25 0 0 1-.659 1.591L5 14.5M9.75 3.104c-.251.023-.501.05-.75.082m.75-.082a24.301 24.301 0 0 1 4.5 0m0 0v5.714c0 .597.237 1.17.659 1.591L19.8 15.3M14.25 3.104c.251.023.501.05.75.082M19.8 15.3l-1.57.393A9.065 9.065 0 0 1 12 15a9.065 9.065 0 0 0-6.23-.693L5 14.5m14.8.8 1.402 1.402c1.232 1.232.65 3.318-1.067 3.611A48.309 48.309 0 0 1 12 21c-2.773 0-5.491-.235-8.135-.687-1.718-.293-2.3-2.379-1.067-3.61L5 14.5" />'],
            ['name' => 'Documentary', 'label' => 'documentary', 'icon' => '<path stroke-linecap="round" stroke-linejoin="round" d="M12 6.042A8.967 8.967 0 0 0 6 3.75c-1.052 0-2.062.18-3 .512v14.25A8.987 8.987 0 0 1 6 18c2.305 0 4.408.867 6 2.292m0-14.25a8.966 8.966 0 0 1 6-2.292c1.052 0 2.062.18 3 .512v14.25A8.987 8.987 0 0 0 18 18a8.967 8.967 0 0 0-6 2.292m0-14.25v14.25" />'],
        ];

        foreach ($categoriesData as $categoryData){
            $category = new Category();
            $category->setName($categoryData['name']);
            $category->setLabel($categoryData['label']);
            $category->setIcon($categoryData['icon']);

            $manager->persist($category);
            $categories[] = $category;
        }
    }

    protected function createLanguages(ObjectManager $manager, array &$languages): void
    {
        $languagesData = [
            ['name' => 'English', 'code' => 'EN'],
            ['name' => 'French', 'code' => 'FR'],
            ['name' => 'Spanish', 'code' => 'ES'],
            ['name' => 'German', 'code' => 'DE'],
            ['name' => 'Italian', 'code' => 'IT'],
            ['name' => 'Portuguese', 'code' => 'PT'],
            ['name' => 'Russian', 'code' => 'RU'],
            ['name' => 'Japanese', 'code' => 'JA'],
            ['name' => 'Korean', 'code' => 'KO'],
            ['name' => 'Arabic', 'code' => 'AR'],
        ];

        foreach ($languagesData as $languageData) {
            $language = new Language();
            $language->setName($languageData['name']);
            $language->setCode($languageData['code']);

            $manager->persist($language);
            $languages[] = $language;
        }
    }

    protected function createMedias(ObjectManager $manager, array &$medias): void
    {
        $staffData = [
            ['name' => 'Martin Scorsese', 'role' => 'Réalisateur', 'image' => 'https://i.pravatar.cc/500/150?u=Martin+Scorsese'],
            ['name' => 'Quentin Tarantino', 'role' => 'Scénariste', 'image' => 'https://i.pravatar.cc/500/150?u=Quentin+Tarantino'],
            ['name' => 'Hans Zimmer', 'role' => 'Compositeur', 'image' => 'https://i.pravatar.cc/500/150?u=Hans+Zimmer'],
            ['name' => 'Kathleen Kennedy', 'role' => 'Producteur', 'image' => 'https://i.pravatar.cc/500/150?u=Kathleen+Kennedy'],
            ['name' => 'Roger Deakins', 'role' => 'Directeur de la photographie', 'image' => 'https://i.pravatar.cc/500/150?u=Roger+Deakins'],
            ['name' => 'Thelma Schoonmaker', 'role' => 'Monteur', 'image' => 'https://i.pravatar.cc/500/150?u=Thelma+Schoonmaker'],
            ['name' => 'Colleen Atwood', 'role' => 'Costumier', 'image' => 'https://i.pravatar.cc/500/150?u=Colleen+Atwood'],
            ['name' => 'Rick Baker', 'role' => 'Maquilleur', 'image' => 'https://i.pravatar.cc/500/150?u=Rick+Baker'],
            ['name' => 'Zoë Bell', 'role' => 'Cascades', 'image' => 'https://i.pravatar.cc/500/150?u=Zoe+Bell'],
        ];

        $castingData = [
            ['name' => 'Ryan Gosling', 'role' => 'Acteur', 'image' => 'https://i.pravatar.cc/150?u=Ryan+Gosling'],
            ['name' => 'Ana de Armas', 'role' => 'Actrice', 'image' => 'https://i.pravatar.cc/150?u=Ana+de+Armas'],
            ['name' => 'Michael Fassbender', 'role' => 'Acteur', 'image' => 'https://i.pravatar.cc/150?u=Michael+Fassbender'],
            ['name' => 'Scarlett Johansson', 'role' => 'Actrice', 'image' => 'https://i.pravatar.cc/150?u=Scarlett+Johansson'],
            ['name' => 'Leonardo DiCaprio', 'role' => 'Acteur', 'image' => 'https://i.pravatar.cc/150?u=Leonardo+DiCaprio'],
            ['name' => 'Emma Stone', 'role' => 'Actrice', 'image' => 'https://i.pravatar.cc/150?u=Emma+Stone'],
            ['name' => 'Denzel Washington', 'role' => 'Acteur', 'image' => 'https://i.pravatar.cc/150?u=Denzel+Washington'],
            ['name' => 'Margot Robbie', 'role' => 'Actrice', 'image' => 'https://i.pravatar.cc/150?u=Margot+Robbie'],
            ['name' => 'Tom Hardy', 'role' => 'Acteur', 'image' => 'https://i.pravatar.cc/150?u=Tom+Hardy'],
        ];

        for ($mediaNumber = 0; $mediaNumber < self::MAX_MEDIA; $mediaNumber++) {
            $mediaType = $this->faker->randomElement([Movie::class, Serie::class]);
            $media = new $mediaType();

            $media->setTitle($this->faker->word);
            $media->setShortDescription($this->faker->text(100));
            $media->setLongDescription($this->faker->text(500));
            $media->setReleaseDate($this->faker->dateTimeBetween('-5 years', 'now'));
            $media->setCoverImage('https://picsum.photos/id/'.random_int(1, 100).'/800/1200');

            $staff = [];
            shuffle($staffData);
            $staff = array_slice($staffData, 0, random_int(2, 5));
            $media->setStaff($staff);
    
            $casting = [];
            shuffle($castingData);
            $casting = array_slice($castingData, 0, random_int(3, 8));
            $media->setCasting($casting);

            if ($media instanceof Serie) {
                $this->createSeasons($manager, $media);
            }

            $manager->persist($media);
            $medias[] = $media;
        }
    }

    //
    // Dependent create methods
    //

    protected function createSubscriptions(ObjectManager $manager, array $users, array &$subscriptions): void
    {
        $subscriptionsData = [
            ['name' => 'Abonnement Découverte', 'durationInMonths' => 1, 'price' => 10],
            ['name' => 'Abonnement Standard', 'durationInMonths' => 3, 'price' => 25],
            ['name' => 'Abonnement Premium', 'durationInMonths' => 6, 'price' => 50],
            ['name' => 'Abonnement Annuel', 'durationInMonths' => 12, 'price' => 100]
        ];

        foreach ($subscriptionsData as $subscriptionData) {
            $subscription = new Subscription();
            $subscription->setName($subscriptionData['name']);
            $subscription->setDurationInMonths($subscriptionData['durationInMonths']);
            $subscription->setPrice($subscriptionData['price']);

            $manager->persist($subscription);
            $subscriptions[] = $subscription;

            for ($subscriptionNumber = 0; $subscriptionNumber < random_int(1, self::MAX_SUBSCRIPTIONS); $subscriptionNumber++) {
                $randomUser = $this->faker->randomElement($users);
                $randomUser->setCurrentSubscription($subscription);
            }
        }
    }

    protected function createPlaylists(ObjectManager $manager, array $users, array &$playlists): void
    {
        foreach ($users as $user) {
            for ($playlistNumber = 0; $playlistNumber < random_int(0, self::MAX_PLAYLISTS_PER_USER); $playlistNumber++) {
                $playlist = new Playlist();
                $playlist->setName($this->faker->word . ' playlist');
                $playlist->setCreatedAt(new DateTimeImmutable());
                $playlist->setUpdatedAt(new DateTimeImmutable());
                $playlist->setCreator($user);

                $manager->persist($playlist);
                $playlists[] = $playlist;
            }
        }
    }

    protected function createComments(ObjectManager $manager, array $medias, array $users): void
    {
        foreach ($medias as $media) {
            for ($commentNumber = 0; $commentNumber < random_int(1, self::MAX_COMMENTS_PER_MEDIA); $commentNumber++) {
                $comment = new Comment();
                $comment->setContent($this->faker->paragraph);
                $comment->setStatus($this->faker->randomElement([CommentStatusEnum::VALIDATED, CommentStatusEnum::PENDING]));
                $comment->setPublisher($this->faker->randomElement($users));
                $comment->setMedia($media);

                $shouldHaveParent = random_int(0, 5) < 2;
                if ($shouldHaveParent) {
                    $parentComment = new Comment();
                    $parentComment->setContent($this->faker->sentence);
                    $parentComment->setStatus($this->faker->randomElement([CommentStatusEnum::VALIDATED, CommentStatusEnum::PENDING]));
                    $parentComment->setPublisher($this->faker->randomElement($users));
                    $parentComment->setMedia($media);

                    $comment->setParentComment($parentComment);
                    $manager->persist($parentComment);
                }

                $manager->persist($comment);
            }
        }
    }

    protected function createSubscriptionHistories(ObjectManager $manager, array $users, array $subscriptions): void
    {
        foreach ($users as $user) {
            $subscription = $this->faker->randomElement($subscriptions);

            for ($subscriptionHistoryNumber = 0; $subscriptionHistoryNumber < random_int(1, self::MAX_SUBSCRIPTIONS_HISTORY_PER_USER); $subscriptionHistoryNumber++) {
                $subscriptionHistory = new SubscriptionHistory();
                $subscriptionHistory->setSubscriber($user);
                $subscriptionHistory->setSubscription($subscription);
                $subscriptionHistory->setStartAt(new DateTimeImmutable());
                $subscriptionHistory->setEndAt((new DateTimeImmutable())->modify('+' . $subscription->getDurationInMonths() . ' months'));

                $manager->persist($subscriptionHistory);
            }
        }
    }

    protected function createPlaylistSubscriptions(ObjectManager $manager, array $users, array $playlists): void
    {
        foreach ($users as $user) {
            for ($playlistSubscriptionNumber = 0; $playlistSubscriptionNumber < random_int(0, self::MAX_PLAYLIST_SUBSCRIPTION_PER_USERS); $playlistSubscriptionNumber++) {
                $playlistSubscription = new PlaylistSubscription();
                $playlistSubscription->setSubscriber($user);
                $playlistSubscription->setPlaylist($this->faker->randomElement($playlists));
                $playlistSubscription->setSubscribedAt(new DateTimeImmutable());

                $manager->persist($playlistSubscription);
            }
        }
    }

    protected function createWatchHistories(ObjectManager $manager, array $users, array $medias): void
    {
        foreach ($users as $user) {
            foreach ($medias as $media) {
                $watchHistory = new WatchHistory();
                $watchHistory->setLastWatchedAt(new DateTimeImmutable());
                $watchHistory->setNumberOfViews(random_int(0, 3));
                $watchHistory->setWatcher($user);
                $watchHistory->setMedia($media);

                $manager->persist($watchHistory);
            }
        }
    }

    protected function createPlaylistMedias(ObjectManager $manager, array $medias, array $playlists): void
    {
        foreach ($medias as $media) {
            for ($mediaNumber = 0; $mediaNumber < random_int(1, self::MAX_MEDIA_PER_PLAYLIST); $mediaNumber++) {
                $playlistMedia = new PlaylistMedia();
                $playlistMedia->setMedia($media);
                $playlistMedia->setAddedAt(new DateTimeImmutable());
                $playlistMedia->setPlaylist($this->faker->randomElement($playlists));

                $manager->persist($playlistMedia);
            }
        }
    }

    protected function createSeasons(ObjectManager $manager, Serie $media): void
    {
        for ($seasonNumber = 0; $seasonNumber < random_int(1, self::MAX_SEASONS); $seasonNumber++) {
            $season = new Season();
            $season->setNumber($seasonNumber + 1);
            $season->setSerie($media);

            $manager->persist($season);
            $this->createEpisodes($manager, $season);
        }
    }

    protected function createEpisodes(ObjectManager $manager, Season $season): void
    {
        for ($episodeNumber = 0; $episodeNumber < random_int(1, self::MAX_EPISODES); $episodeNumber++) {
            $episode = new Episode();
            $episode->setTitle('Episode '.($episodeNumber + 1).' - '.$this->faker->sentence);
            $episode->setDurationInMinutes(random_int(10, 70));
            $episode->setReleasedAt(DateTimeImmutable::createFromFormat('Y-m-d', $this->faker->date('Y-m-d')));
            $episode->setSeason($season);

            $manager->persist($episode);
        }
    }

    //
    // Link methods
    //

    protected function linkMediaToCategories(array $medias, array $categories): void
    {
        foreach ($medias as $media) {
            for ($categoryNumber = 0; $categoryNumber < random_int(1, self::MAX_CATEGORY_PER_MEDIA); $categoryNumber++) {
                $media->addCategory($this->faker->randomElement($categories));
            }
        }
    }

    protected function linkMediaToLanguages(array $medias, array $languages): void
    {
        foreach ($medias as $media) {
            for ($languageNumber = 0; $languageNumber < random_int(1, self::MAX_LANGUAGE_PER_MEDIA); $languageNumber++) {
                $media->addLanguage($this->faker->randomElement($languages));
            }
        }
    }
}