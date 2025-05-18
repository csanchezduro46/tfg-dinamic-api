import { ComponentFixture, TestBed } from '@angular/core/testing';

import { GenericSuccessPopupComponent } from './generic-success-popup.component';

describe('GenericSuccessPopupComponent', () => {
  let component: GenericSuccessPopupComponent;
  let fixture: ComponentFixture<GenericSuccessPopupComponent>;

  beforeEach(async () => {
    await TestBed.configureTestingModule({
      imports: [GenericSuccessPopupComponent]
    })
    .compileComponents();

    fixture = TestBed.createComponent(GenericSuccessPopupComponent);
    component = fixture.componentInstance;
    fixture.detectChanges();
  });

  it('should create', () => {
    expect(component).toBeTruthy();
  });
});
