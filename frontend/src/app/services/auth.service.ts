import { Injectable } from '@angular/core';
import { HttpClient } from '@angular/common/http';

@Injectable({
  providedIn: 'root'
})
export class AuthService {

  private apiUrl = 'http://localhost/Sistema/backend';

  constructor(private http: HttpClient) { }

  login(email: string, password: string) {
    return this.http.post<any>(
      `${this.apiUrl}/auth/login.php`,
      { email, password }
    );
  }

  saveToken(token: string) {
    localStorage.setItem('token', token);
  }

  getToken(): string | null {
    return localStorage.getItem('token');
  }

  logout() {
    localStorage.removeItem('token');
  }

  isLoggedIn(): boolean {
    return !!this.getToken();
  }

  getUserRole(): 'admin' | 'user' | null {
    const token = this.getToken();
    if (!token) return null;

    try {
      const payload = JSON.parse(atob(token.split('.')[1]));
      return payload.role ?? null;
    } catch {
      return null;
    }
  }

  isAdmin(): boolean {
    return this.getUserRole() === 'admin';
  }

  saveRole(role: string) {
    localStorage.setItem('role', role);
  }
  register(email: string, password: string) {
    return this.http.post(
      'http://localhost/Sistema/backend/auth/register.php',
      {
        email,
        password
      },
      {
        headers: {
          'Content-Type': 'application/json'
        }
      }
    );
  }
}


