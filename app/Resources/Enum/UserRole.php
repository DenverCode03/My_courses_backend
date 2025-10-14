<?php
namespace App\Resources\Enum;

enum UserRole : string {
    case Tuteur = "tuteur";
    case Etudiant = "étudiant";
}