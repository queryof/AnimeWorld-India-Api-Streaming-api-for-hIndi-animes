# Anime World India Project

This project is a web application for browsing and watching anime series and movies. It leverages PHP for backend API routing and HTML/CSS/JavaScript for the frontend UI.

## Deployment

To deploy and run this project, you will need a local web server environment (e.g., XAMPP, WAMP, MAMP) that supports PHP.

1.  **Clone the Repository:**
    ```bash
    git clone <your-repository-url>
    ```

2.  **Place in Web Server Directory:**
    Move the entire project folder (`htdocs`) into your web server's document root (e.g., `C:\xampp\htdocs` for XAMPP).

3.  **Start Web Server:**
    Ensure your Apache and PHP services are running in your XAMPP/WAMP/MAMP control panel.

4.  **Access in Browser:**
    Open your web browser and navigate to `http://localhost/` (or `http://localhost/your_project_folder_name/` if you placed it in a subfolder).

## API Documentation

All API endpoints are located under `api/anime-world-india/v1/`.

### 1. `a2z.php`

*   **Endpoint:** `/api/anime-world-india/v1/a2z.php`
*   **Method:** `GET`
*   **Description:** Fetches a list of anime series and movies starting with a specific letter or number, with pagination support.

*   **Parameters:**
    *   `letter` (string, required): The starting letter (e.g., `a`, `b`) or `0-9` for numbers.
    *   `page` (int, optional): The page number for pagination. Defaults to `1`.

*   **Example Request:**
    ```
    http://localhost/api/anime-world-india/v1/a2z.php?letter=a&page=1
    http://localhost/api/anime-world-india/v1/a2z.php?letter=0-9
    ```

*   **Example Success Response:**
    ```json
    {
        "success": true,
        "letter": "a",
        "current_page": 1,
        "total_pages": 5,
        "has_next": true,
        "has_prev": false,
        "total_results": 20,
        "results": [
            {
                "title": "Anime Title 1",
                "rating": "TMDB 7.5",
                "year": "2023",
                "poster": "https://example.com/poster1.jpg",
                "url": "https://animeworld-india.me/series/anime-title-1",
                "id": "series/anime-title-1",
                "type": "series"
            },
            {
                "title": "Movie Title 1",
                "rating": "TMDB 8.0",
                "year": "2022",
                "poster": "https://example.com/poster2.jpg",
                "url": "https://animeworld-india.me/movie/movie-title-1",
                "id": "movie/movie-title-1",
                "type": "movie"
            }
        ]
    }
    ```

*   **Example Error Response:**
    ```json
    {
        "success": false,
        "error": "letter parameter is required"
    }
    ```

### 2. `episodes.php`

*   **Endpoint:** `/api/anime-world-india/v1/episodes.php`
*   **Method:** `GET`
*   **Description:** Fetches details for a specific season of an anime series, including its episodes.

*   **Parameters:**
    *   `seasonId` (string, required): The ID of the season to fetch details for (e.g., `one-punch-man-season-1`).

*   **Example Request:**
    ```
    http://localhost/api/anime-world-india/v1/episodes.php?seasonId=one-punch-man-season-1
    ```

*   **Example Success Response:**
    ```json
    {
        "success": true,
        "source": "animeworld-india.me/season",
        "season": {
            "seasonId": "one-punch-man-season-1",
            "animeTitle": "One-Punch Man",
            "seasonName": "Season 1",
            "totalEpisodes": "12",
            "rating": "8.8",
            "duration": "24 min/ep",
            "poster": "https://example.com/one-punch-man-s1-poster.jpg",
            "description": "The story of Saitama, a hero who can defeat any enemy with a single punch..."
        },
        "episodes": [
            {
                "episodeId": "one-punch-man-s1-episode-1",
                "title": "The Strongest Man",
                "episodeNumber": "Episode 1",
                "airDate": "Oct 5, 2015",
                "image": "https://example.com/one-punch-man-s1-ep1.jpg",
                "overview": "Saitama, a hero for fun, faces a monster on a rampage..."
            },
            {
                "episodeId": "one-punch-man-s1-episode-2",
                "title": "The Lone Cyborg",
                "episodeNumber": "Episode 2",
                "airDate": "Oct 12, 2015",
                "image": "https://example.com/one-punch-man-s1-ep2.jpg",
                "overview": "Saitama encounters Genos, a cyborg seeking revenge..."
            }
        ]
    }
    ```

*   **Example Error Response:**
    ```json
    {
        "success": false,
        "error": "Missing seasonId parameter"
    }
    ```

### 3. `home.php`

*   **Endpoint:** `/api/anime-world-india/v1/home.php`
*   **Method:** `GET`
*   **Description:** Fetches the latest anime series and movies displayed on the homepage.

*   **Parameters:** None

*   **Example Request:**
    ```
    http://localhost/api/anime-world-india/v1/home.php
    ```

*   **Example Success Response:**
    ```json
    {
        "success": true,
        "source": "animeworld-india.me",
        "latest_series": [
            {
                "title": "Latest Series Title 1",
                "image": "https://example.com/latest-series1.jpg",
                "year": "2024",
                "rating": "TMDB 8.2",
                "seriesId": "latest-series-title-1"
            }
        ],
        "latest_movies": [
            {
                "title": "Latest Movie Title 1",
                "image": "https://example.com/latest-movie1.jpg",
                "year": "2024",
                "rating": "TMDB 8.5",
                "movieId": "latest-movie-title-1"
            }
        ]
    }
    ```

*   **Example Error Response:**
    ```json
    {
        "success": false,
        "error": "Failed to load HTML"
    }
    ```

### 4. `movie.php`

*   **Endpoint:** `/api/anime-world-india/v1/movie.php`
*   **Method:** `GET`
*   **Description:** Fetches a paginated list of movies. This API appears to fetch a general list of movies, not details for a single movie.

*   **Parameters:**
    *   `p` (int, optional): The page number for pagination. Defaults to `1`.

*   **Example Request:**
    ```
    http://localhost/api/anime-world-india/v1/movie.php?p=1
    ```

*   **Example Success Response:**
    ```json
    {
        "success": true,
        "source": "animeworld-india.me/movies",
        "current_page": 1,
        "total_pages": 10,
        "has_next": true,
        "has_prev": false,
        "pages": [1, 2, 3, 4, 5, 6, 7, 8, 9, 10],
        "total_results": 20,
        "movies": [
            {
                "title": "Movie Title A",
                "image": "https://example.com/movieA.jpg",
                "year": "2021",
                "rating": "TMDB 7.2",
                "movieId": "movie-title-a"
            }
        ]
    }
    ```

*   **Example Error Response:**
    ```json
    {
        "success": false,
        "error": "Failed to load HTML"
    }
    ```

### 5. `search.php`

*   **Endpoint:** `/api/anime-world-india/v1/search.php`
*   **Method:** `GET`
*   **Description:** Searches for anime series and movies based on a query string, with pagination support.

*   **Parameters:**
    *   `query` (string, required): The search query (e.g., `one punch man`).
    *   `p` (int, optional): The page number for pagination. Defaults to `1`.

*   **Example Request:**
    ```
    http://localhost/api/anime-world-india/v1/search.php?query=jujutsu%20kaisen&p=1
    ```

*   **Example Success Response:**
    ```json
    {
        "success": true,
        "query": "jujutsu kaisen",
        "currentPage": 1,
        "totalPages": 3,
        "hasNextPage": true,
        "source": "animeworld-india.me/search",
        "results": [
            {
                "title": "Jujutsu Kaisen",
                "image": "https://example.com/jujutsu-kaisen.jpg",
                "year": "2020",
                "rating": "TMDB 8.7",
                "type": "series",
                "seriesId": "jujutsu-kaisen"
            }
        ]
    }
    ```

*   **Example Error Response:**
    ```json
    {
        "success": false,
        "error": "Missing query parameter"
    }
    ```

### 6. `seasons.php`

*   **Endpoint:** `/api/anime-world-india/v1/seasons.php`
*   **Method:** `GET`
*   **Description:** Retrieves detailed information about a specific anime series, including its seasons.

*   **Parameters:**
    *   `seriesID` (string, required): The ID of the anime series (e.g., `one-punch-man`).

*   **Example Request:**
    ```
    http://localhost/api/anime-world-india/v1/seasons.php?seriesID=one-punch-man
    ```

*   **Example Success Response:**
    ```json
    {
        "success": true,
        "source": "animeworld-india.me/series",
        "series": {
            "seriesId": "one-punch-man",
            "title": "One-Punch Man",
            "poster": "https://example.com/one-punch-man-poster.jpg",
            "year": "2015",
            "duration": "24 min",
            "rating": "8.8",
            "totalSeasons": "2",
            "description": "The story of Saitama, a hero who can defeat any enemy with a single punch..."
        },
        "seasons": [
            {
                "seasonNumber": "1",
                "seasonName": "Season 1",
                "episodes": "12 Episodes",
                "seasonId": "one-punch-man-season-1"
            }
        ]
    }
    ```

*   **Example Error Response:**
    ```json
    {
        "success": false,
        "error": "Missing seriesID parameter"
    }
    ```

### 7. `series.php`

*   **Endpoint:** `/api/anime-world-india/v1/series.php`
*   **Method:** `GET`
*   **Description:** Fetches a paginated list of anime series.

*   **Parameters:**
    *   `p` (int, optional): The page number for pagination. Defaults to `1`.

*   **Example Request:**
    ```
    http://localhost/api/anime-world-india/v1/series.php?p=1
    ```

*   **Example Success Response:**
    ```json
    {
        "success": true,
        "source": "animeworld-india.me/series",
        "current_page": 1,
        "total_pages": 50,
        "has_next": true,
        "has_prev": false,
        "pages": [1, 2, 3, 4, 5, 6, 7, 8, 9, 10],
        "total_results": 20,
        "series": [
            {
                "title": "Anime Series A",
                "image": "https://example.com/seriesA.jpg",
                "year": "2020",
                "rating": "TMDB 8.0",
                "seriesId": "anime-series-a"
            }
        ]
    }
    ```

*   **Example Error Response:**
    ```json
    {
        "success": false,
        "error": "Empty response"
    }
    ```

### 8. `stream.php`

*   **Endpoint:** `/api/anime-world-india/v1/stream.php`
*   **Method:** `GET`
*   **Description:** Fetches streaming and download links for a specific movie or episode, along with its details and related series/episode information.

*   **Parameters:**
    *   `episodeId` (string, optional): The ID of the episode (e.g., `one-punch-man-s1-episode-1`).
    *   `movieId` (string, optional): The ID of the movie (e.g., `jujutsu-kaisen-0-movie`).
    *   **Note:** Either `episodeId` or `movieId` is required.

*   **Example Request (Episode):**
    ```
    http://localhost/api/anime-world-india/v1/stream.php?episodeId=one-punch-man-s1-episode-1
    ```

*   **Example Success Response (Episode):**
    ```json
    {
        "success": true,
        "type": "episode",
        "source": "animeworld-india.me/episode",
        "series": {
            "title": "One-Punch Man",
            "poster": "https://example.com/one-punch-man-s1-poster.jpg",
            "season": "Season 1",
            "totalEpisodes": "12",
            "rating": "8.8",
            "duration": "24 min",
            "description": "The story of Saitama, a hero who can defeat any enemy with a single punch..."
        },
        "current": {
            "episodeId": "one-punch-man-s1-episode-1",
            "title": "The Strongest Man",
            "airDate": "Oct 5, 2015",
            "overview": "Saitama, a hero for fun, faces a monster on a rampage..."
        },
        "previous": null,
        "next": "one-punch-man-s1-episode-2",
        "episodes": [
            {
                "episodeId": "one-punch-man-s1-episode-1",
                "title": "The Strongest Man",
                "episodeNumber": "Episode 1",
                "airDate": "Oct 5, 2015",
                "image": "https://example.com/one-punch-man-s1-ep1.jpg"
            }
        ],
        "stream": {
            "streamLink": "https://stream.example.com/one-punch-man-s1-ep1.mp4",
            "file": "https://download.example.com/one-punch-man-s1-ep1.mp4"
        }
    }
    ```

*   **Example Request (Movie):**
    ```
    http://localhost/api/anime-world-india/v1/stream.php?movieId=jujutsu-kaisen-0-movie
    ```

*   **Example Success Response (Movie):**
    ```json
    {
        "success": true,
        "type": "movie",
        "source": "animeworld-india.me/movie",
        "movie": {
            "movieId": "jujutsu-kaisen-0-movie",
            "title": "Jujutsu Kaisen 0 Movie",
            "description": "Yuta Okkotsu, a high school student, gains control of a powerful cursed spirit...",
            "year": "2021",
            "duration": "105 min",
            "rating": "8.3"
        },
        "stream": {
            "streamLink": "https://stream.example.com/jujutsu-kaisen-0.mp4",
            "file": "https://download.example.com/jujutsu-kaisen-0.mp4"
        }
    }
    ```

*   **Example Error Response:**
    ```json
    {
        "success": false,
        "error": "Missing episodeId or movieId"
    }
    ```

