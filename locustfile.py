from locust import HttpUser, task, between
import random
 
class BiblioUser(HttpUser):
    wait_time = between(3, 5)  # Délai moyen entre actions
 
    def on_start(self):
        # Connexion une seule fois au début
        self.client.post("/login", {
            "email": "user@biblioconnect.fr",
            "password": "password"
        }, verify=False)
 
    @task(5)  # 3–5 fois : Rechercher un livre
    def search_books(self):
        self.client.get("/book/", verify=False)
 
    @task(2)  # 2–3 fois : Consulter avis
    def view_book_detail(self):
        book_id = random.randint(1, 5)
        self.client.get(f"/book/{book_id}", verify=False)
 
    @task(1)  # 1 fois : Réserver un livre après plusieurs recherches
    def reserve_book(self):
        if random.randint(1, 5) == 1:  # 1 fois sur 5 recherches
            book_id = random.randint(1, 5)
            self.client.post(f"/reservation/new/{book_id}", verify=False)
 
    @task(1)  # Voir mes réservations
    def view_reservations(self):
        self.client.get("/reservation/", verify=False)
    @task(1)  # Ajouter un commentaire simulé
    def add_comment(self):
        book_id = random.randint(1, 5)
        self.client.post(f"/comment/add/{book_id}", {
            "content": "Très bon livre !"
        }, verify=False)
