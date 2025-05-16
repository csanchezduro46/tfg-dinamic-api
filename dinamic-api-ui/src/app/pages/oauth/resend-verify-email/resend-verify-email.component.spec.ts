import { ComponentFixture, TestBed } from '@angular/core/testing';

import { ResendVerifyEmailComponent } from './resend-verify-email.component';

describe('ResendVerifyEmailComponent', () => {
  let component: ResendVerifyEmailComponent;
  let fixture: ComponentFixture<ResendVerifyEmailComponent>;

  beforeEach(async () => {
    await TestBed.configureTestingModule({
      imports: [ResendVerifyEmailComponent]
    })
    .compileComponents();

    fixture = TestBed.createComponent(ResendVerifyEmailComponent);
    component = fixture.componentInstance;
    fixture.detectChanges();
  });

  it('should create', () => {
    expect(component).toBeTruthy();
  });
});
