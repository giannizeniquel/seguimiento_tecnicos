import { Component, Output, EventEmitter, HostListener, ElementRef } from '@angular/core';
import { CommonModule } from '@angular/common';
import { Router, RouterModule } from '@angular/router';
import { AuthService } from '../../../core/services/auth.service';
import { IUser } from '../../../core/models';

@Component({
  selector: 'app-navbar',
  standalone: true,
  imports: [CommonModule, RouterModule],
  templateUrl: './navbar.component.html',
  styleUrls: ['./navbar.component.scss']
})
export class NavbarComponent {
  @Output() sidebarToggle = new EventEmitter<void>();

  currentUser: IUser | null = null;
  notificationMenuOpen = false;
  profileMenuOpen = false;

  constructor(
    private authService: AuthService,
    private router: Router,
    private elementRef: ElementRef
  ) {
    this.currentUser = this.authService.getCurrentUserValue();
    this.authService.currentUser$.subscribe({
      next: (user: IUser | null) => {
        this.currentUser = user;
      },
      error: (error) => {
        console.error('Error getting current user', error);
      }
    });
  }

  logout(): void {
    this.authService.logout().subscribe(() => {
      this.authService.logoutClient();
      this.router.navigate(['/login']);
    });
  }

  toggleSidebar(): void {
    this.sidebarToggle.emit();
  }

  toggleNotificationMenu(): void {
    this.notificationMenuOpen = !this.notificationMenuOpen;
    this.profileMenuOpen = false;
  }

  toggleProfileMenu(): void {
    this.profileMenuOpen = !this.profileMenuOpen;
    this.notificationMenuOpen = false;
  }

  toggleDarkMode(): void {
    document.body.classList.toggle('dark');
  }

  @HostListener('document:click', ['$event'])
  onDocumentClick(event: MouseEvent): void {
    if (!this.elementRef.nativeElement.contains(event.target)) {
      this.notificationMenuOpen = false;
      this.profileMenuOpen = false;
    }
  }
}

