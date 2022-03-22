import pandas as pd

csv_files_folder = "Excel"
file_to_normalize = "movies.csv"

file_dir = csv_files_folder+'/'+file_to_normalize
read_file = pd.read_csv(file_dir)

count_index = 1
genres_dictionary = {}
genres = pd.DataFrame(columns=['genres', 'genresId'])

for row in read_file["genres"]:
    if '|' in row:
        genres_in_row = row.split('|')

        for value in genres_in_row:
            if value not in genres_dictionary.keys():
                genres_dictionary[value] = count_index
                count_index+=1
    else:
        if row not in genres_dictionary.keys():
            genres_dictionary[row] = count_index
            count_index+=1

genres_data = pd.DataFrame(list(genres_dictionary.items()),columns = ['genres','genresId'])
genres_data = genres_data.set_index('genres')
genres_data.to_csv("Excel/genres.csv")


movieId_genresId = pd.DataFrame(columns=['movieId', 'genresId'])
two_columns = read_file.iloc[:, [0,-1]]

for index, row in two_columns.iterrows():
    movie_id = row[0]
    all_genres = row[1]

    if '|' in all_genres:
        genres_in_row = all_genres.split('|')

        for genre in genres_in_row:
            value = genres_dictionary[genre]
            movieId_genresId = movieId_genresId.append({'movieId': movie_id, 'genresId': value}, ignore_index=True)

    else:
        value = genres_dictionary[all_genres]
        movieId_genresId = movieId_genresId.append({'movieId': movie_id, 'genresId': value}, ignore_index=True)

movieId_genresId = movieId_genresId.set_index('movieId')
movieId_genresId.to_csv("Excel/movieId_genresId.csv")


movie_dataframe = read_file

year_list = []
title_list = []

for row in read_file["title"]:
    reverse_string = row[::-1]

    year = ""
    title = ""

    for each_char in reverse_string:
        if each_char.isdigit():
            year += each_char

        if len(year) == 4:
            break

    year = year[::-1]
        
    if len(year) != 4:
        year = "-1"

    title = row.replace('('+year+')', '')
        
    year_list.append(year)
    title_list.append(title)

movie_dataframe["title"] = title_list
movie_dataframe["year"] = year_list
movie_dataframe.pop("genres")

#movie_dataframe = movie_dataframe.set_index('movieId')

movie_dataframe.to_csv("Excel/copy_movie.csv", sep="|")
'''

'''
def each_move_rating(file_dir):
    read_file = pd.read_csv(file_dir)
    two_columns = read_file.iloc[:, [1,2]]

    rating_dict = {}
    number_of_appearances = {}

    for index, row in two_columns.iterrows():
        movie_id = row[0]
        rating = row[1]

        if movie_id in rating_dict:
            rating_dict[movie_id] += rating
            number_of_appearances[movie_id] += 1
        else:
            rating_dict[movie_id] = rating
            number_of_appearances[movie_id] = 1

    for movie_id in rating_dict:
        movie_appearances = number_of_appearances[movie_id]
        movie_value = rating_dict[movie_id]

        movie_value = movie_value / movie_appearances
        movie_value = round(movie_value, 2)

        rating_dict[movie_id] = movie_value
        
    #print(rating_dict)
    return rating_dict

file_dir = csv_files_folder+'/'+"ratings_movie.csv"

ratings = each_move_rating(file_dir)

def append_rating_to_movie_table(rating_dict, file_dir):
    read_file = pd.read_csv(file_dir, delimiter="|")
    read_file["ratings"] = ""

    count = 0
    for cell in read_file["movieId"]:
        if cell in rating_dict:
            value = rating_dict[cell]
        else:
            value = 0
        read_file.iat[count,4] = value
        count += 1

    read_file.pop("Unnamed: 0")
    read_file = read_file.set_index('movieId')
    read_file.to_csv("Excel/copy_movie.csv", sep="|")

    return

file_dir = csv_files_folder+'/'+"copy_movie.csv"
append_rating_to_movie_table(ratings, file_dir)
