<div class="movie-match">
    <?php
    session_start();
    $found_user_id = isset($_SESSION['found_user']) ? $_SESSION['found_user'] : "";

    if (isset($_SESSION['user_id'])) {
        $user_id = $_SESSION['user_id'];
    }

    $connection = mysqli_connect("localhost", "root", "root", "local");

    if (!$connection) {
        die("Ошибка подключения к базе данных: " . mysqli_connect_error());
    }

    if (isset($user_id)) {
        $user1_id = $user_id;
    } else {
        $user1_id = "Для просмотра нужно авторизоваться";
    }

    if (isset($_SESSION['found_user_id'])) {
        $user2_id = $_SESSION['found_user_id'];
    } else {
        $user2_id = "Укажите юзера для поиска в настройках";
    }



    $query1 = "SELECT movie_id FROM user_likes WHERE user_id = '$user1_id'";

    $query2 = "SELECT movie_id FROM user_likes WHERE user_id = '$user2_id'";

    $result1 = mysqli_query($connection, $query1);
    $result2 = mysqli_query($connection, $query2);

    if ($result1 && $result2) {
        $user1_movies = [];
        $user2_movies = [];

        while ($row = mysqli_fetch_assoc($result1)) {
            $user1_movies[] = $row['movie_id'];
        }

        while ($row = mysqli_fetch_assoc($result2)) {
            $user2_movies[] = $row['movie_id'];
        }

        $common_movies = array_intersect($user1_movies, $user2_movies);

        echo "<h2>Common movies between users $user1_id and $user2_id: </h2>";
        echo "<div class='wishlist'>";

        // foreach ($common_movies as $movie_id) {
        //     $api_key = "19cc2d55ec287216302aaf07144d9835";
        //     $api_url = "https://api.themoviedb.org/3/movie/$movie_id?api_key=$api_key";

        //     $api_response = file_get_contents($api_url);

        //     $api_data = json_decode($api_response, true);

        //     if (isset($api_data['title'])) {
        //         echo "<li class='wishlist-movie' id = ".$movie_id.">" . $api_data['title'] . "</li>";
        //     }
        // }
        $common_movies_reversed = array_reverse($common_movies);

        foreach ($common_movies_reversed as $movie_id) {
            $api_key = "19cc2d55ec287216302aaf07144d9835";
            $api_url = "https://api.themoviedb.org/3/movie/$movie_id?api_key=$api_key";
            $moviePosterUrl = "https://image.tmdb.org/t/p/w500/";

            $api_response = file_get_contents($api_url);

            $api_data = json_decode($api_response, true);

            if (isset($api_data['title']) && isset($api_data['poster_path'])) {
                $movieTitle = $api_data['title'];
                $posterPath = $api_data['poster_path'];
                $releaseYear = date('Y', strtotime($api_data['release_date']));
                echo "<div class='wishlist-movie' id='$movie_id'>";
                echo "<div class='img-film'>";
                echo "<img class='film-image' src='$moviePosterUrl$posterPath' alt='$movieTitle' title='$movieTitle'>";
                echo "<div class='description'>";
                echo "<h1>$movieTitle</h1>";
                echo "<h5>$releaseYear</h5>";
                echo "</div>";
                echo "</div>";
                echo "</div>";
            }
        }

        echo "</div>";
    } else {
        echo "Ошибка при выполнении запросов: " . mysqli_error($connection);
    }

    mysqli_close($connection);
    ?>
</div>