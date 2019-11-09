<?php

namespace mQueue\Model;

use DOMDocument;
use DOMXPath;
use Exception;
use Zend_Date;

/**
 * A movie
 */
class Movie extends AbstractModel
{
    /**
     * Extract IMDb id from URL
     *
     * @param string $string
     *
     * @return null|string the id extracted
     */
    public static function extractId(?string $string): ?string
    {
        if (!$string) {
            return null;
        }

        $string = self::paddedId($string);
        preg_match_all("/(\d{7,})/", $string, $r);
        if (isset($r[1][0])) {
            return $r[1][0];
        }

        return null;
    }

    /**
     * Returns the ID for IMDb with padded 0
     *
     * @param string $id
     *
     * @return string the id extracted
     */
    public static function paddedId(string $id): string
    {
        return str_pad($id, 7, '0', STR_PAD_LEFT);
    }

    /**
     * Returns the title, if needed fetch the title from IMDb
     *
     * @return string
     */
    public function getTitle()
    {
        // If we didn't get the title yet, fetch it and save in our database
        if (!($this->title)) {
            $this->fetchData();
        }

        return $this->title;
    }

    /**
     * Fetch data from IMDb and store in database (possibly overwriting)
     */
    public function fetchData(): void
    {
        $ch = curl_init($this->getImdbUrl());
        curl_setopt($ch, CURLOPT_HTTPHEADER, ['Accept-Language: en-US,en;q=0.8']);
        curl_setopt($ch, CURLOPT_HEADER, false);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        $file = curl_exec($ch);
        curl_close($ch);

        $document = new DOMDocument();
        @$document->loadHTML($file);
        $xpath = new DOMXPath($document);

        // Extract title
        $titleEntries = $xpath->evaluate('//meta[contains(@property, "og:title")]/@content');
        if ($titleEntries->length == 1) {
            $rawTitle = $titleEntries->item(0)->value;
            $this->title = preg_replace('~ - IMDb$~', '', $rawTitle);
        } else {
            $this->title = '[title not available, could not fetch from IMDb]';

            return; // If there is not even title give up everything
        }

        // Extract release date
        $jsonLd = $xpath->evaluate('//script[@type="application/ld+json"]/text()');
        if ($jsonLd->length == 1) {
            $json = json_decode($jsonLd->item(0)->data, true);
            $this->dateRelease = $json['datePublished'] ?? null;
        } else {
            $this->dateRelease = null;
        }

        $this->dateUpdate = Zend_Date::now()->get(Zend_Date::ISO_8601);
        $this->setReadOnly(false); // If the movie is coming from a joined query, we need to set non-readonly before saving
        $this->save();
    }

    /**
     * Sets the ID for the movie from any string containing a valid ID
     *
     * @param string $id
     *
     * @return Movie
     */
    public function setId($id)
    {
        $extractedId = self::extractId($id);
        if (!$extractedId) {
            throw new Exception(sprintf('Invalid Id for movie. Given "%1$s", extracted "%2$s"', $id, $extractedId));
        }

        $this->id = $extractedId;

        return $this;
    }

    /**
     * Returns the IMDb url for the movie
     *
     * @return string
     */
    public function getImdbUrl(): string
    {
        return 'https://www.imdb.com/title/tt' . self::paddedId($this->id) . '/';
    }

    /**
     * Returns the status for this movie and the specified user
     *
     * @param User $user
     *
     * @return Status
     */
    public function getStatus(User $user = null)
    {
        return StatusMapper::find($this->id, $user);
    }

    /**
     * Set the status for the specified user
     *
     * @param User $user
     * @param int $rating @see \mQueue\Model\Status
     *
     * @return Status
     */
    public function setStatus(User $user, $rating)
    {
        $status = StatusMapper::set($this, $user, $rating);

        return $status;
    }

    /**
     * Set the source for the movie if any. In any case record the search date and count
     *
     * @param array|false $source
     */
    public function setSource($source): void
    {
        $this->dateSearch = Zend_Date::now()->get(Zend_Date::ISO_8601);
        ++$this->searchCount;
        if ($source && @$source['score']) {
            $this->identity = $source['identity'];
            $this->quality = $source['quality'];
            $this->score = $source['score'];
            $this->source = $source['link'];
        }
    }
}
