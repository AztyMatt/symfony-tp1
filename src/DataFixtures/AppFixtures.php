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
            $user->setPassword(password_hash($this->faker->password, PASSWORD_BCRYPT));
            $user->setAccountStatus(UserAccountStatusEnum::ACTIVE);
            $user->setProfilePicture('https://i.pravatar.cc/150?u='.$user->getUsername());

            $manager->persist($user);
            $users[] = $user;
        }
    }

    protected function createCategories(ObjectManager $manager, array &$categories): void
    {
        $categoriesData = [
            ['name' => 'Aventure', 'label' => 'aventure'],
            ['name' => 'Action', 'label' => 'action'],
            ['name' => 'Comedy', 'label' => 'comedy'],
            ['name' => 'Drama', 'label' => 'drama'],
            ['name' => 'Horror', 'label' => 'horror'],
            ['name' => 'Thriller', 'label' => 'thriller'],
            ['name' => 'Romance', 'label' => 'romance'],
            ['name' => 'Sci-Fi', 'label' => 'sci-fi'],
            ['name' => 'Fantasy', 'label' => 'fantasy'],
            ['name' => 'Documentary', 'label' => 'documentary'],
        ];

        foreach ($categoriesData as $categoryData){
            $category = new Category();
            $category->setName($categoryData['name']);
            $category->setLabel($categoryData['label']);

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
            $media->setCoverImage('https://picsum.photos/id/'.random_int(1, 100).'/400/600');

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