import { Component, OnInit } from '@angular/core';
import { CommonModule } from '@angular/common';
import { FormsModule } from '@angular/forms';
import { AppointmentsService } from '../../services/appointments.service';
import { AuthService } from '../../services/auth.service';
import { catchError } from 'rxjs/operators';  // Add this import
import { throwError } from 'rxjs';  // Add this import

@Component({
  standalone: true,
  selector: 'app-appointments',
  imports: [CommonModule, FormsModule],
  templateUrl: './appointments.component.html',
  styleUrls: ['./appointments.component.scss'],
  template: `
    <h2>Turnos</h2>

    <!-- Optional: Display error messages to the user -->
    <div *ngIf="errorMessage" class="error">{{ errorMessage }}</div>

    <form (ngSubmit)="create()">
      <input type="number" [(ngModel)]="client_id" name="client_id" placeholder="Client ID" required>
      <input type="date" [(ngModel)]="date" name="date" required>
      <input type="time" [(ngModel)]="time" name="time" required>

      <button type="submit">Crear turno</button>
    </form>

    <hr>

    <ul>
      <li *ngFor="let a of appointments">
        {{ a.date }} {{ a.time }} - {{ a.client }} →
        <strong>{{ a.status }}</strong>

        <!-- SOLO ADMIN -->
        <span *ngIf="isAdmin">
          <button 
            *ngIf="a.status === 'pending'"
            (click)="updateStatus(a.id, 'confirmed')">
            Confirmar
          </button>
          <button 
            *ngIf="a.status === 'pending'"
            (click)="updateStatus(a.id, 'cancelled')">
            Cancelar
          </button>
        </span>
      </li>
    </ul>
  `
})
export class AppointmentsComponent implements OnInit {

  appointments: any[] = [];
  isAdmin = false;
  errorMessage = '';  // New: For displaying errors to the user

  client_id!: number;
  date = '';
  time = '';

  constructor(
    private service: AppointmentsService,
    private auth: AuthService
  ) { }

  ngOnInit() {
    this.isAdmin = this.auth.getUserRole() === 'admin';
    this.load();
  }

  load() {
    this.service.getAppointments()
      .pipe(
        catchError(error => {
          console.error('Error loading appointments:', error);
          this.errorMessage = 'Error al cargar turnos. Revisa la consola para detalles.';
          return throwError(error);  // Re-throw to prevent further processing
        })
      )
      .subscribe(data => {
        this.appointments = data;
        this.errorMessage = '';  // Clear error on success
      });
  }

  create() {
    this.service.createAppointment({
      client_id: this.client_id,
      date: this.date,
      time: this.time
    })
      .pipe(
        catchError(error => {
          console.error('Error creating appointment:', error);
          this.errorMessage = 'Error al crear turno. Revisa la consola para detalles.';
          return throwError(error);
        })
      )
      .subscribe(() => {
        this.load();  // Reload only on success
        this.errorMessage = '';
        // Optional: Reset form
        this.client_id = 0;
        this.date = '';
        this.time = '';
      });
  }

  updateStatus(id: number, status: string) {
    this.service.updateStatus(id, status)
      .pipe(
        catchError(error => {
          console.error('Error updating status:', error);
          this.errorMessage = 'Error al actualizar estado. Revisa la consola para detalles.';
          return throwError(error);
        })
      )
      .subscribe(() => {
        this.load();  // Reload only on success
        this.errorMessage = '';
      });
  }
}