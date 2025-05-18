import { ComponentFixture, TestBed } from '@angular/core/testing';

import { GenericErrorPopupComponent } from './generic-error-popup.component';

describe('GenericErrorPopupComponent', () => {
  let component: GenericErrorPopupComponent;
  let fixture: ComponentFixture<GenericErrorPopupComponent>;

  beforeEach(async () => {
    await TestBed.configureTestingModule({
      imports: [GenericErrorPopupComponent]
    })
    .compileComponents();

    fixture = TestBed.createComponent(GenericErrorPopupComponent);
    component = fixture.componentInstance;
    fixture.detectChanges();
  });

  it('should create', () => {
    expect(component).toBeTruthy();
  });
});
