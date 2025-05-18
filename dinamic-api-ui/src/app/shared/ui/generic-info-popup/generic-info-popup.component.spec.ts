import { ComponentFixture, TestBed } from '@angular/core/testing';

import { GenericInfoPopupComponent } from './generic-info-popup.component';

describe('GenericInfoPopupComponent', () => {
  let component: GenericInfoPopupComponent;
  let fixture: ComponentFixture<GenericInfoPopupComponent>;

  beforeEach(async () => {
    await TestBed.configureTestingModule({
      imports: [GenericInfoPopupComponent]
    })
    .compileComponents();

    fixture = TestBed.createComponent(GenericInfoPopupComponent);
    component = fixture.componentInstance;
    fixture.detectChanges();
  });

  it('should create', () => {
    expect(component).toBeTruthy();
  });
});
