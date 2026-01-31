import { Component, Input, Output, EventEmitter } from '@angular/core';
import { CommonModule } from '@angular/common';

@Component({
  selector: 'app-modal-dialog',
  standalone: true,
  templateUrl: './modal-dialog.component.html',
  styleUrls: ['./modal-dialog.component.scss'],
  imports: [CommonModule]
})
export class ModalDialogComponent {
  private _open = false;
  @Input() set open(value: boolean) {
    this._open = value;
    console.log('ModalDialog open set to', value);
  }
  get open(): boolean {
    return this._open;
  }
  @Input() title = '';
  @Output() close = new EventEmitter<void>();

  onClose(): void {
    this.close.emit();
  }
}
