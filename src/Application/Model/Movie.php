<?php

namespace Application\Model;

use Application\Enum\Rating;
use Application\Repository\MovieRepository;
use Cake\Chronos\Chronos;
use Doctrine\ORM\Mapping as ORM;
use Exception;

/**
 * A movie.
 */
#[ORM\Entity(MovieRepository::class)]
class Movie extends AbstractModel
{
    /**
     * Extract IMDb id from URL.
     *
     * @return null|numeric-string the id extracted
     */
    public static function extractId(int|string|null $string): ?string
    {
        if (!$string) {
            return null;
        }

        $string = self::paddedId($string);
        preg_match_all('/(\\d{7,})/', $string, $r);
        if (isset($r[1][0])) {
            return $r[1][0];
        }

        return null;
    }

    /**
     * Returns the ID for IMDb with padded 0.
     *
     * @return string the id extracted
     */
    public static function paddedId(int|string $id): string
    {
        return mb_str_pad((string) $id, 7, '0', STR_PAD_LEFT);
    }

    /**
     * Returns the title, if needed fetch the title from IMDb.
     */
    #[ORM\Column(type: 'string', length: 512)]
    public string $title {
        get {
            // If we didn't get the title yet, fetch it and save in our database
            if (!$this->title) {
                $this->fetchData();
            }

            return $this->title;
        }
    }

    #[ORM\Column(type: 'smallint', nullable: true, options: ['unsigned' => true])]
    public ?int $year;

    #[ORM\Column(type: 'datetime', nullable: true)]
    public ?Chronos $dateSearch = null;

    #[ORM\Column(type: 'smallint', options: ['unsigned' => true, 'default' => 0])]
    public int $searchCount = 0;

    #[ORM\Column(type: 'smallint', options: ['unsigned' => true, 'default' => 0])]
    public int $identity = 0;

    #[ORM\Column(type: 'smallint', options: ['unsigned' => true, 'default' => 0])]
    public int $quality = 0;

    #[ORM\Column(type: 'smallint', options: ['unsigned' => true, 'default' => 0])]
    public int $score = 0;

    #[ORM\Column(type: 'string', length: 1024, nullable: true)]
    public ?string $source = null;

    /**
     * Fetch data from IMDb and store in database (possibly overwriting).
     */
    public function fetchData(): void
    {
        $couldNotFetch = '[title not available, could not fetch from IMDb]';

        $data = $this->fetchJson();
        $title = @$data['Title'];

        // Extract title
        if ($title) {
            $this->title = $title;
        } else {
            $this->title = $couldNotFetch;

            return; // If there is not even title give up everything
        }

        // Extract release date
        $year = (int) @$data['Year'];
        if ($year) {
            $this->year = $year;
            $this->title .= " ($year)";
        } else {
            $this->year = null;
        }

        $this->dateUpdate = Chronos::now();
        _em()->flush();
    }

    private function fetchJson(): ?array
    {
        global $container;
        $apiKey = $container->get('config')['apiKey'];
        if (!$apiKey) {
            return null;
        }

        $url = "https://www.omdbapi.com/?apikey=$apiKey&i=tt" . self::paddedId($this->id);
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_HTTPHEADER, ['Accept-Language: en-US,en;q=0.8']);
        curl_setopt($ch, CURLOPT_HEADER, false);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        $file = curl_exec($ch);
        if (!$file) {
            return null;
        }

        return json_decode($file, true, flags: JSON_THROW_ON_ERROR);
    }

    /**
     * Sets the ID for the movie from any string containing a valid ID.
     */
    public function setId(int $id): void
    {
        $extractedId = self::extractId($id);
        if (!$extractedId) {
            throw new Exception(sprintf('Invalid Id for movie. Given "%1$s", extracted "%2$s"', $id, $extractedId));
        }

        $this->id = (int) $extractedId;
    }

    /**
     * Returns the IMDb url for the movie.
     */
    public function getImdbUrl(): string
    {
        return 'https://www.imdb.com/title/tt' . self::paddedId($this->id) . '/';
    }

    /**
     * Returns the status for this movie and the specified user.
     */
    public function getStatus(?User $user = null): Status
    {
        return _em()->getRepository(Status::class)->getOneByMovieAndUser($this->id, $user);
    }

    /**
     * Set the status for the specified user.
     */
    public function setStatus(User $user, Rating $rating): Status
    {
        $status = _em()->getRepository(Status::class)->set($this, $user, $rating);

        return $status;
    }

    /**
     * Set the source for the movie if any. In any case record the search date and count.
     */
    public function setSource(array|false $source): void
    {
        $this->dateSearch = Chronos::now();
        ++$this->searchCount;
        if ($source && @$source['score']) {
            $this->identity = $source['identity'];
            $this->quality = $source['quality'];
            $this->score = $source['score'];
            $this->source = $source['link'];
        }
    }
}
