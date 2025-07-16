from locust import HttpUser, task, between
import random

class BiblioUser(HttpUser):
    wait_time = between(3, 5)

    def on_start(self):
        self.client.post("/login", {
            "email": "user@biblioconnect.fr",
            "password": "password"
        }, verify=False)

    @task(5)
    def search_books(self):
        self.client.get("/book/", verify=False)

    @task(2)
    def view_book_detail(self):
        book_id = random.randint(1, 5)
        self.client.get(f"/book/{book_id}", verify=False)

    @task(1)
    def reserve_book(self):
        if random.randint(1, 5) == 1:
            book_id = random.randint(1, 5)
            self.client.post(f"/reservation/new/{book_id}", verify=False)

    @task(1)
    def view_reservations(self):
        self.client.get("/reservation/", verify=False)

    @task(1)
    def add_comment(self):
        book_id = random.randint(1, 5)
        self.client.post(f"/comment/add/{book_id}", {
            "content": "Très bon livre !"
        }, verify=False)

class LibrarianUser(HttpUser):
    wait_time = between(3, 5)

    def on_start(self):
        self.client.post("/login", {
            "email": "librarien@biblioconnect.fr",
            "password": "password"
        }, verify=False)

    @task(2)
    def view_authors(self):
        self.client.get("/author/", verify=False)

    @task(1)
    def create_author(self):
        self.client.post("/author/new", {
            "author[firstName]": f"Test{random.randint(1, 1000)}",
            "author[lastName]": f"Author{random.randint(1, 1000)}"
        }, verify=False)

    @task(1)
    def edit_author(self):
        author_id = random.randint(1, 5)
        self.client.post(f"/author/{author_id}/edit", {
            "author[firstName]": f"Edit{random.randint(1, 1000)}",
            "author[lastName]": f"Author{random.randint(1, 1000)}"
        }, verify=False)

class AdminUser(HttpUser):
    wait_time = between(3, 5)

    def on_start(self):
        self.client.post("/login", {
            "email": "admin@biblioconnect.fr",
            "password": "password"
        }, verify=False)

    @task(2)
    def view_users(self):
        self.client.get("/admin/users/", verify=False)

    @task(1)
    def change_user_role(self):
        user_id = random.randint(1, 5)
        self.client.post(f"/admin/users/{user_id}/role", {
            "role": "ROLE_USER",
            "_token": "valid_csrf_token"
        }, verify=False)

    @task(1)
    def delete_user(self):
        user_id = random.randint(1, 5)
        self.client.post(f"/admin/users/{user_id}", {
            "_token": "valid_csrf_token"
        }, verify=False)
