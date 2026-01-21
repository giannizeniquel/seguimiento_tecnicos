import { Component } from '@angular/core';
import { CommonModule } from '@angular/common';
import { Router } from '@angular/router';
import { AuthService } from '../../../core/services/auth.service';
import { IUser } from '../../../core/models';

@Component({
  selector: 'app-navbar',
  standalone: true,
  imports: [CommonModule],
  templateUrl: './navbar.component.html',
  styleUrls: ['./navbar.component.scss']
})
export class NavbarComponent {
  currentUser: IUser | null = null;
  isMenuOpen = false;

  constructor(
    private authService: AuthService,
    private router: Router,
    @Inject(AuthService) private _authService: AuthService
  ) {
    this.authService = _authService;
    this.currentUser = this.authService.getCurrentUserValue();
    this.authService.currentUser$.subscribe((user: IUser | null) => {
      this.currentUser = user;
    });
  }

  logout(): void {
    this.authService.logout().subscribe(() => {
      this.authService.logoutClient();
      this.router.navigate(['/login']);
    });
  }

  toggleMenu(): void {
    this.isMenuOpen = !this.isMenuOpen;
  }

  closeMenu(): void {
    this.isMenuOpen = false;
  }
}
