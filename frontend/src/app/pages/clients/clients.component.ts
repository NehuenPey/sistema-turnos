import { Component, OnInit } from '@angular/core';
import { CommonModule } from '@angular/common';
import { ClientsService } from '../../services/clients.service';

@Component({
  standalone: true,
  selector: 'app-clients',
  imports: [CommonModule],
  template: `
    <h2>Clientes</h2>

    <ul>
      <li *ngFor="let c of clients">
        {{ c.name }} - {{ c.email }}
      </li>
    </ul>
  `
})
export class ClientsComponent implements OnInit {

  clients: any[] = [];

  constructor(private clientsService: ClientsService) {}

  ngOnInit() {
    this.clientsService.getClients()
      .subscribe(data => {
        this.clients = data;
      });
  }
}
