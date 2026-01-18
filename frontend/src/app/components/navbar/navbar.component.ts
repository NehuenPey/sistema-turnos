import { Component } from '@angular/core';
import { CommonModule } from '@angular/common';
import { RouterLink, Router } from '@angular/router';
import { AuthService } from '../../services/auth.service';

@Component({
  standalone: true,
  selector: 'app-navbar',
  imports: [CommonModule, RouterLink],
  template: `
    <nav class="nav" *ngIf="isLoggedIn">

      <a *ngIf="isAdmin" routerLink="/clients">Clientes</a>

      <a routerLink="/appointments">
        {{ isAdmin ? 'Turnos' : 'Mis turnos' }}
      </a>

      <button (click)="logout()">Salir</button>
    </nav>
  `,
  styles: [`
    .nav {
      display: flex;
      gap: 15px;
      padding: 10px;
      background: #222;
      color: white;
      align-items: center;
    }

    a {
      color: white;
      text-decoration: none;
    }

    button {
      margin-left: auto;
      background: #c62828;
      border: none;
      color: white;
      padding: 5px 10px;
      cursor: pointer;
    }
  `]
})
export class NavbarComponent {

  isAdmin = false;
  isLoggedIn = false;

  constructor(
    private auth: AuthService,
    private router: Router
  ) {
    this.isLoggedIn = this.auth.isLoggedIn();
    this.isAdmin = this.auth.isAdmin();
  }

  logout() {
    this.auth.logout();
    this.router.navigate(['/login']);
  }
}
