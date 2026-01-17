import { Injectable } from '@angular/core';
import { HttpClient } from '@angular/common/http';

interface JwtPayload {
  user_id: number;
  role: string;
  exp: number;
}

@Injectable({
  providedIn: 'root'
})
export class AuthService {

  private apiUrl = 'http://localhost/Sistema/backend';

  constructor(private http: HttpClient) {}

  login(email: string, password: string) {
    return this.http.post<{ token: string }>(
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

  /** Decodifica el payload del JWT */
  private getPayload(): JwtPayload | null {
    const token = this.getToken();
    if (!token) return null;

    try {
      const payload = token.split('.')[1];
      return JSON.parse(atob(payload));
    } catch {
      return null;
    }
  }

  /** Rol del usuario */
  getRole(): string | null {
    return this.getPayload()?.role ?? null;
  }

  /** Solo admin */
  isAdmin(): boolean {
    return this.getRole() === 'admin';
  }
  
  getUserRole(): string | null {
    const token = this.getToken();
    if (!token) return null;

    try {
      const payload = JSON.parse(atob(token.split('.')[1]));
      return payload.role || null;
    } catch {
      return null;
    }
  }
  saveRole(role: string) {
    localStorage.setItem('role', role);
  }
}
